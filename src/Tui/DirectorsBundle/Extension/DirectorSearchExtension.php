<?php

namespace Tui\DirectorsBundle\Extension;

use Doctrine\DBAL\Connection;
use Symfony\Component\Routing\Router;
use Symfony\Bundle\DoctrineBundle\Registry;

class DirectorSearchExtension
{
    protected $search = null;
    protected $dbh    = null;
    protected $router = null;
    protected $em     = null;
    
    
    protected $page_length = 100;
    
    
    
    public function __construct(\SphinxClient $search, Connection $connection, Router $router, Registry $doctrine)
    {
        $this->search = $search;
        $this->dbh    = $connection;
        $this->router = $router;
        $this->em     = $doctrine->getEntityManager();
    }
    
    
    public function searchAll($query, $page = 1)
    {
        
        // Configure pagination
        $page_info = array(
            'query'       => $query,
            'page_length' => $this->page_length,
            'page'        => $page,
            'offset'      => ($page - 1) * $this->page_length,
            'route'        => 'search_all',
        );

        // Do the search
        $this->search->setLimits($page_info['offset'], $this->page_length);
        $appointees_results = $this->search->Query($query, 'appointees');
        $companies_results  = $this->search->Query($query, 'companies');

        // Fill out remainining pagination info
        $page_info['appointees_found'] = $appointees_results['total'];
        $page_info['companies_found']  = $companies_results['total'];
        $page_info['appointees_end']   = (($page_info['offset'] + $page_info['page_length']) > $appointees_results['total']) ? $appointees_results['total'] : $page_info['offset'] + $page_info['page_length'];
        $page_info['companies_end']    = (($page_info['offset'] + $page_info['page_length']) > $companies_results['total']) ? $companies_results['total'] : $page_info['offset'] + $page_info['page_length'];
        $page_info['has_more_appointees'] = ($appointees_results['total'] > $page_info['appointees_end']);
        $page_info['has_more_companies'] = ($companies_results['total'] > $page_info['companies_end']);
        
        $out = array(
            'query'     => $query, 
            'page_info' => $page_info,
        );
        
        $out = array_merge($out, $this->loadAppointeeObjects($appointees_results));
        $out = array_merge($out, $this->loadCompanyObjects($companies_results));
        
        return $out;
    }
    

    public function searchCompanies($query, $page = 1)
    {
        
        // Configure pagination
        $page_info = array(
            'query'       => $query,
            'page_length' => $this->page_length,
            'page'        => $page,
            'offset'      => ($page - 1) * $this->page_length,
            'route'        => 'search_companies',
        );

        // Do the search
        $this->search->setLimits($page_info['offset'], $this->page_length);
        $companies_results  = $this->search->Query($query, 'companies');

        // Fill out remainining pagination info
        $page_info['companies_found']  = $companies_results['total'];
        $page_info['companies_end']    = (($page_info['offset'] + $page_info['page_length']) > $companies_results['total']) ? $companies_results['total'] : $page_info['offset'] + $page_info['page_length'];
        $page_info['has_more_companies'] = ($companies_results['total'] > $page_info['companies_end']);
        
        
        $out = array(
            'query'     => $query, 
            'page_info' => $page_info,
        );
        
        $out = array_merge($out, $this->loadCompanyObjects($companies_results));
        
        return $out;        
    }
    
    public function searchAppointees($query, $page)
    {
        
        // Configure pagination
        $page_info = array(
            'query'       => $query,
            'page_length' => $this->page_length,
            'page'        => $page,
            'offset'      => ($page - 1) * $this->page_length,
            'route'        => 'search_appointees',
        );

        // Do the search
        $this->search->setLimits($page_info['offset'], $this->page_length);
        $appointees_results = $this->search->Query($query, 'appointees');

        // Fill out remainining pagination info
        $page_info['appointees_found'] = $appointees_results['total'];
        $page_info['appointees_end']   = (($page_info['offset'] + $page_info['page_length']) > $appointees_results['total']) ? $appointees_results['total'] : $page_info['offset'] + $page_info['page_length'];
        $page_info['has_more_appointees'] = ($appointees_results['total'] > $page_info['appointees_end']);
        
        
        $out = array(
            'query'     => $query, 
            'page_info' => $page_info,
        );
        
        $out = array_merge($out, $this->loadAppointeeObjects($appointees_results));
        
        return $out;        
    }
    
    
    
    public function toJSON($results)
    {
        $out = array('appointees' => null, 'companies' => null);
        
        if (isset($results['appointees']))
        {
          $out['appointees'] = array();
          foreach($results['appointees'] as $a)
          {
            $out['appointees'][] = array(
              'url'           => $this->router->generate('appointee_show', array('_format' => 'json', 'id' => $a->getId()), true),
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
        
        
        if (isset($results['companies']))
        {
          $out['companies'] = array();
          foreach($results['companies'] as $c)
          {
            $out['companies'][] = array(
              'id'   => $c->getId(),
              'url'  => $this->router->generate('company_show', array('_format' => 'json', 'id' => $c->getId()), true),
              'name' => $c->getName(),
            );
          }
        }

        $out['urls'] = array();
        if ($results['page_info']['page'] > 1)
        {
            $out['urls']['prev_page'] = array(
                'href' => $this->router->generate($results['page_info']['route'], array(
                        '_format' => 'json', 
                        'q'       => $results['page_info']['query'], 
                        'page'    => $results['page_info']['page'] - 1,
                    )
                ),
                'title' => 'Previous results',
                'type' => 'application/json',
            );
        }

        if (
            (isset($results['page_info']['has_more_appointees']) && $results['page_info']['has_more_appointees']) || 
            (isset($results['page_info']['has_more_companies']) && $results['page_info']['has_more_companies'])
           )
        {
            $out['urls']['next_page'] = array(
                'href' => $this->router->generate($results['page_info']['route'], array(
                        '_format' => 'json', 
                        'q'       => $results['page_info']['query'], 
                        'page'    => $results['page_info']['page'] + 1,
                    )
                ),
                'title' => 'Next results',
                'type' => 'application/json',
            );
        }

        
        return $out;
    }
    

    public function loadCompanyObjects($results)
    {
        // Gather company document ids, turn them into company ids
        $company_ids = array(); $companies = null; $company_appointees = array();
        if (isset($results['matches']))
        {
          $company_ids = array_map(function($v){return $v['id'];}, $results['matches']);

          $ids = $this->dbh->fetchAll('SELECT ch_number FROM sphinx_company_index WHERE document_id IN ('.join($company_ids,',').')');

          $ids = array_map(function($v){return $v['ch_number'];}, $ids);

          $ex = $this->em->getExpressionBuilder();
          $q = $this->em->createQuery('SELECT c FROM TuiDirectorsBundle:Company c WHERE '.$ex->in('c.id', $ids));
          $companies = $q->getResult();


          // Get truncated appointees for companies
          $r = $this->em->getRepository('TuiDirectorsBundle:Company');

          
          foreach($companies as $c)
          {
              $company_appointees[ $c->getId() ] = $r->getAbbreviatedAppointees($c, 5);
          }
        }
        
        return array(
            'companies'          => $companies, 
            'company_appointees' => $company_appointees,
        );
    }

    public function loadAppointeeObjects($results)
    {
        // Gather appointee document ids, turn them into appointee ids
        $appointee_ids = array(); $appointees = null; $appointee_companies = array();
        if (isset($results['matches']))
        {
          $appointee_ids = array_map(function($v){return $v['id'];}, $results['matches']);

          $ids = $this->dbh->fetchAll('SELECT ch_number FROM sphinx_appointee_index WHERE document_id IN ('.join($appointee_ids,',').')');

          $ids = array_map(function($v){return $v['ch_number'];}, $ids);

          $ex = $this->em->getExpressionBuilder();
          $q = $this->em->createQuery('SELECT a
              FROM TuiDirectorsBundle:Appointee a
              WHERE '.$ex->in('a.id', $ids));
          $appointees = $q->getResult();
          
          // Get truncated companies for appointees
          $r = $this->em->getRepository('TuiDirectorsBundle:Appointee');

          
          foreach($appointees as $a)
          {
              $appointee_companies[ $a->getId() ] = $r->getAbbreviatedCompanies($a, 5);
          }
        }
        
        
        return array(
            'appointees'          => $appointees, 
            'appointee_companies' => $appointee_companies,
        );
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