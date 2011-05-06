<?php

namespace Tui\DirectorsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
  
    /**
     * @extra:Route("/", name="home")
     * @extra:Template()
     */
  
    public function indexAction()
    {
        return array();
    }
    
    /**
     * @extra:Route("/privacy", name="privacy")
     * @extra:Template()
     */
  
    public function privacyAction()
    {
        return array();
    }


    
    /**
     * @extra:Route("/search/all.{_format}", name="search_all", defaults={"_format" = "html"})
     * @extra:Template()
     */
  
    public function searchAction()
    {
        $dbh   = $this->get('database_connection');
        $em    = $this->get('doctrine.orm.entity_manager');
        $query = $this->get('request')->get('q');
        
        if (!trim($query))
        {
            return $this->redirect($this->generateUrl('home'));
        }
        
        // Get the search service and execute the query
        $sphinx     = $this->get('search');
        $appointees = $sphinx->Query($query, 'appointees');
        $companies  = $sphinx->Query($query, 'companies');


        // Gather appointee document ids, turn them into appointee ids
        $appointee_ids = array();
        if ($appointees['total_found'])
        {
          $appointee_ids = array_map(function($v){return $v['id'];}, $appointees['matches']);

          $ids = $dbh->fetchAll('SELECT ch_number FROM sphinx_appointee_index WHERE document_id IN ('.join($appointee_ids,',').')');

          $ids = array_map(function($v){return $v['ch_number'];}, $ids);

          $ex = $em->getExpressionBuilder();
          $q = $em->createQuery('SELECT a FROM TuiDirectorsBundle:Appointee a WHERE '.$ex->in('a.id', $ids));
          $appointees = $q->getArrayResult();
          
          $appointees = array_map(function($v){ if ($v['dateOfBirth']) $v['dateOfBirth'] = $v['dateOfBirth']->format('Y'); return $v; }, $appointees);
        }


        // Gather company document ids, turn them into company ids
        $company_ids = array();
        if ($companies['total_found'])
        {
          $company_ids = array_map(function($v){return $v['id'];}, $companies['matches']);

          $ids = $dbh->fetchAll('SELECT ch_number FROM sphinx_company_index WHERE document_id IN ('.join($company_ids,',').')');

          $ids = array_map(function($v){return $v['ch_number'];}, $ids);

          $ex = $em->getExpressionBuilder();
          $q = $em->createQuery('SELECT c FROM TuiDirectorsBundle:Company c WHERE '.$ex->in('c.id', $ids));
          $companies = $q->getArrayResult();
        }




      
        return array('appointees' => $appointees, 'companies' => $companies, 'query' => $query);
    }


    
    
    
}
