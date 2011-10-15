<?php
namespace Tui\DirectorsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;


class RebuildCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('whorunsit:rebuild')
            ->setDescription('Rebuild company, appointee, and appointment tables from data')
            ->addArgument('dir', InputArgument::REQUIRED, 'Path to data files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('dir');
        
        if (!is_dir($dir))
        {
            throw new \Exception('Invalid directory specified');
        }

        $finder = new Finder();
        $finder->files()->in($dir)->name('*.txt');

        foreach ($finder as $file) {
            $this->parseFile($file, $output);
        }

    }
    
    
    
    protected function parseFile($file, $output)
    {
        $output->writeln(sprintf('<info>Parsing %s</info>', $file->getFilename()));

        $parser = new chDirectorsParser($file->getRealpath());
        $output->writeln((string)$parser.PHP_EOL);
        
        // Get DBAL
        $connection = $this->getContainer()->get('database_connection'); 


        $output->writeln('Preparing queries…');
        $new_company = $connection->prepare('INSERT INTO company (id, status, officers, name) VALUES (:id,:status,:officers,:name)');
        $new_person  = $connection->prepare(
          'INSERT INTO appointee 
          (id, revision, postcode, date_of_birth, title, 
           forenames, surname, honours, care_of, po_box, address_1, 
           address_2, town, county, country, occupation, nationality, 
           residence, is_corporate
          ) VALUES 
          (:id, :revision, :postcode, :date_of_birth, :title, 
           :forenames, :surname, :honours, :care_of, :po_box, :address_1, 
           :address_2, :town, :county, :country, :occupation, :nationality,
           :residence, :is_corporate)
        ');
        $new_appointment = $connection->prepare(
          'INSERT INTO company_appointment 
          (company_id, appointee_id, type, appointed_on, appointment_date_source, resigned_on) 
          VALUES 
          (:company, :appointment, :type, :appointed_on, :appointment_date_source, :resigned_on)'
        );
        $revision_exists = $connection->prepare('SELECT count(*) FROM appointee WHERE id = ? AND revision = ?');
 
		$output->writeln('Processing…');        
        $record_count = 0;
        $connection->beginTransaction();
        foreach($parser as $entry)
        {
          $record_count++;
          if ($record_count % 5000 == 0)
          {
            $output->writeln(sprintf('Processed %d file records. Memory: %0.2fM', $record_count, memory_get_usage()/1024/1024));
          
            try{
              $connection->commit();
            } catch (\Exception $e)
            {
              $connection->rollback();
              throw new \Exception(sprintf('Transaction failed, block ending in line %d of file %s', $record_count, $file));
            }
            $connection->beginTransaction();
          }
          
          
          if ($entry instanceof chCompany)
          {
            $new_company->execute(
              array(
                'id'       =>$entry->id,
                'status'   =>$entry->status,
                'officers' =>$entry->officers,
                'name'     =>$entry->name,
          
              )
            );
          
          } 
          // elseif ($entry instanceof chPerson)
          // {
          // 
          //   $revision_exists->execute(array($entry->id, $entry->revision));
          // 
          //   if (0 == $revision_exists->fetchColumn()) {
          //     $new_person->execute(
          //       array(
          //         'id'                      =>$entry->id, 
          //         'revision'                =>$entry->revision, 
          //         'postcode'                =>$entry->postcode, 
          //         'date_of_birth'           =>$entry->date_of_birth?$entry->date_of_birth->format('Y-m-d'):null, 
          //         'title'                   =>$entry->details['title'], 
          //         'forenames'               =>$entry->details['forenames'], 
          //         'surname'                 =>$entry->details['surname'], 
          //         'honours'                 =>$entry->details['honours'], 
          //         'care_of'                 =>$entry->details['care_of'], 
          //         'po_box'                  =>$entry->details['po_box'], 
          //         'address_1'               =>$entry->details['address_1'], 
          //         'address_2'               =>$entry->details['address_2'], 
          //         'town'                    =>$entry->details['town'], 
          //         'county'                  =>$entry->details['county'], 
          //         'country'                 =>$entry->details['country'], 
          //         'occupation'              =>$entry->details['occupation'], 
          //         'nationality'             =>$entry->details['nationality'], 
          //         'residence'               =>$entry->details['residence'],
          //         'is_corporate'            =>$entry->corporate, 
          // 
          //       )
          //     );
          //   }
          // 
          //   $new_appointment->execute(
          //     array(
          //       'company'                 =>$entry->company_id,
          //       'appointment'             =>$entry->id,
          //       'type'                    =>$entry->appointment_type, 
          //       'appointed_on'            =>$entry->appointment_date->format('Y-m-d'), 
          //       'appointment_date_source' =>$entry->appointment_date_origin, 
          //       'resigned_on'             =>$entry->resignation_date?$entry->resignation_date->format('Y-m-d'):null,
          //     )
          //   );
          // 
          // 
          // }
          // 

        }
     
        $connection->commit();
    }
}