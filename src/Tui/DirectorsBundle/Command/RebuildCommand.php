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
        $db = $this->getContainer()->get('database_connection'); 

        
        
    }
}