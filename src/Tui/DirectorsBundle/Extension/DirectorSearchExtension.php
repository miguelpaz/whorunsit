<?php

namespace Tui\DirectorsBundle\Extension;

use Symfony\Component\DependencyInjection\ContainerAware;


class DirectorSearchExtension extends ContainerAware
{
    protected $search = null;
    
    protected $page_length = 100;
    
    
    
    public function __construct(\SphinxClient $search)
    {
        $this->search = $search;
    }
    
    
    public function searchAll($query, $page = 1)
    {
        $dbh   = $this->container->get('database_connection');
        $em    = $this->container->get('doctrine')->getEntityManager();
        
        
        
        // Configure pagination
        $page_info = array(
            'query'       => $query,
            'page_length' => $this->page_length,
            'page'        => $page,
            'offset'      => ($page - 1) * $this->page_length,
        );

        // Do the search
        $this->search->setLimits($page_info['offset'], $page_info['page_length']);
        $appointees_results = $this->search->Query($query, 'appointees');
        $companies_results  = $this->search->Query($query, 'companies');

        // Fill out remainining pagination info
        $page_info['appointees_found'] = $appointees_results['total'];
        $page_info['companies_found']  = $companies_results['total'];
        $page_info['appointees_end']   = (($page_info['offset'] + $page_info['page_length']) > $appointees_results['total']) ? $appointees_results['total'] : $page_info['offset'] + $page_info['page_length'];
        $page_info['companies_end']    = (($page_info['offset'] + $page_info['page_length']) > $companies_results['total']) ? $companies_results['total'] : $page_info['offset'] + $page_info['page_length'];
        if ($appointees_results['total'] > $companies_results['total'])
        {
            $page_info['has_more_pages'] = ($appointees_results['total'] > $page_info['appointees_end']);
        } else {
            $page_info['has_more_pages'] = ($companies_results['total'] > $page_info['companies_end']);
        }
        

        // Gather appointee document ids, turn them into appointee ids
        $appointee_ids = array(); $appointees = null; $appointee_companies = array();
        if (isset($appointees_results['matches']))
        {
          $appointee_ids = array_map(function($v){return $v['id'];}, $appointees_results['matches']);

          $ids = $dbh->fetchAll('SELECT ch_number FROM sphinx_appointee_index WHERE document_id IN ('.join($appointee_ids,',').')');

          $ids = array_map(function($v){return $v['ch_number'];}, $ids);

          $ex = $em->getExpressionBuilder();
          $q = $em->createQuery('SELECT a
              FROM TuiDirectorsBundle:Appointee a
              WHERE '.$ex->in('a.id', $ids));
          $appointees = $q->getResult();
          
          // Get truncated companies for appointees
          $r = $em->getRepository('TuiDirectorsBundle:Appointee');

          
          foreach($appointees as $a)
          {
              $appointee_companies[ $a->getId() ] = $r->getAbbreviatedCompanies($a, 5);
          }
        }




        // Gather company document ids, turn them into company ids
        $company_ids = array(); $companies = null; $company_appointees = array();
        if (isset($companies_results['matches']))
        {
          $company_ids = array_map(function($v){return $v['id'];}, $companies_results['matches']);

          $ids = $dbh->fetchAll('SELECT ch_number FROM sphinx_company_index WHERE document_id IN ('.join($company_ids,',').')');

          $ids = array_map(function($v){return $v['ch_number'];}, $ids);

          $ex = $em->getExpressionBuilder();
          $q = $em->createQuery('SELECT c FROM TuiDirectorsBundle:Company c WHERE '.$ex->in('c.id', $ids));
          $companies = $q->getResult();


          // Get truncated appointees for companies
          $r = $em->getRepository('TuiDirectorsBundle:Company');

          
          foreach($companies as $c)
          {
              $company_appointees[ $c->getId() ] = $r->getAbbreviatedAppointees($c, 5);
          }
        } 
        
        
        
        
        return array('appointees' => $appointees, 'companies' => $companies, 'query' => $query, 'appointee_companies' => $appointee_companies, 'company_appointees' => $company_appointees, 'page_info' => $page_info);
    }
    
    public function searchCompanies($query, $page)
    {
        
    }
    
    public function searchAppointees($query, $page)
    {
        
    }
    
    
    
    public function toJSON($results)
    {
        $out = array('appointees' => null, 'companies' => null);
        
        if ($results['appointees'])
        {
          $out['appointees'] = array();
          foreach($results['appointees'] as $a)
          {
            $out['appointees'][] = array(
              'url'           => $this->container->get('router')->generate('appointee_show', array('_format' => 'json', 'id' => $a->getId()), true),
              'id'            => $a->getId(),
              'title'         => $a->getTitle(),
              'forenames'     => $a->getForenames(),
              'surname'       => $a->getSurname(),
              'honours'       => $a->getHonours(),
              'date_of_birth' => $a->getDateOfBirth() instanceof \Datetime ? $a->getDateOfBirth()->format('Y') : null,
              'postcode'      => $a->getPostcode() ? substr($a->getPostcode(),0,strpos($a->getPostcode(), ' ')) : null,
        
            );
          }
        }
        
        
        if ($results['companies'])
        {
          $out['companies'] = array();
          foreach($results['companies'] as $c)
          {
            $out['companies'][] = array(
              'id'   => $c->getId(),
              'url'  => $this->container->get('router')->generate('company_show', array('_format' => 'json', 'id' => $c->getId()), true),
              'name' => $c->getName(),
            );
          }
        
        }
        
        return $out;
    }
    
    
    
    
    public function setPageLength($length)
    {
        $this->page_length = (int) $length;
    }
    
    public function getName()
    {
        return 'search';
    }
}