<?php

namespace Tui\DirectorsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


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
        $em    = $this->get('doctrine')->getEntityManager();
        $query = trim(filter_var($this->get('request')->get('q'), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
        
        if (!$query)
        {
            return $this->redirect($this->generateUrl('home'));
        }
        
        // Get the search service and execute the query
        $sphinx             = $this->get('search');
        $appointees_results = $sphinx->Query($query, 'appointees');
        $companies_results  = $sphinx->Query($query, 'companies');


        // Gather appointee document ids, turn them into appointee ids
        $appointee_ids = array();
        if ($appointees_results['total_found'])
        {
          $appointee_ids = array_map(function($v){return $v['id'];}, $appointees_results['matches']);

          $ids = $dbh->fetchAll('SELECT ch_number FROM sphinx_appointee_index WHERE document_id IN ('.join($appointee_ids,',').')');

          $ids = array_map(function($v){return $v['ch_number'];}, $ids);

          $ex = $em->getExpressionBuilder();
          $q = $em->createQuery('SELECT a FROM TuiDirectorsBundle:Appointee a WHERE '.$ex->in('a.id', $ids));
          $appointees = $q->getResult();
        }


        // Gather company document ids, turn them into company ids
        $company_ids = array();
        if ($companies_results['total_found'])
        {
          $company_ids = array_map(function($v){return $v['id'];}, $companies_results['matches']);

          $ids = $dbh->fetchAll('SELECT ch_number FROM sphinx_company_index WHERE document_id IN ('.join($company_ids,',').')');

          $ids = array_map(function($v){return $v['ch_number'];}, $ids);

          $ex = $em->getExpressionBuilder();
          $q = $em->createQuery('SELECT c FROM TuiDirectorsBundle:Company c WHERE '.$ex->in('c.id', $ids));
          $companies = $q->getResult();
        } 


        if ($this->get('request')->getRequestFormat() == 'json')
        {
          $out = array('appointees' => null, 'companies' => null);
          
          if ($appointees)
          {
            $out['appointees'] = array();
            foreach($appointees as $a)
            {
              $out['appointees'][] = array(
                'url'           => $this->generateUrl('appointee_show', array('_format' => 'json', 'id' => $a->getId()), true),
                'id'            => $a->getId(),
                'title'         => $a->getTitle(),
                'forenames'     => $a->getForenames(),
                'surname'       => $a->getSurname(),
                'honours'       => $a->getHonours(),
                'date_of_birth' => $a->getDateOfBirth() instanceof \Datetime ? $a->getDateOfBirth()->format('Y') : null,
                'postcode'      => substr($a->getPostcode(),0,strpos($a->getPostcode(' '))),
          
              );
            }
          }
          
          
          if ($companies)
          {
            $out['companies'] = array();
            foreach($companies as $c)
            {
              $out['companies'][] = array(
                'id'   => $c->getId(),
                'url'  => $this->generateUrl('company_show', array('_format' => 'json', 'id' => $c->getId()), true),
                'name' => $c->getName(),
              );
            }
          
          }
          
          
          if ($this->get('request')->get('callback',false))
          {
            $callback = filter_var($this->get('request')->get('callback'));
            
            return new Response($callback.'('.json_encode($out).')');
          }
          
          return new Response(json_encode($out));
        }




      
        return array('appointees' => $appointees, 'companies' => $companies, 'query' => $query);
    }


    
    
    
}
