<?php

namespace Tui\DirectorsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class DefaultController extends Controller
{
  
    /**
     * @Route("/", name="home")
     * @Template()
     */
  
    public function indexAction()
    {
        return array();
    }
    
    /**
     * @Route("/privacy", name="privacy")
     * @Template()
     */
  
    public function privacyAction()
    {
        return array();
    }


    /**
     * @Route("/faq", name="faq")
     * @Template()
     */
    public function faqAction()
    {
        return array();
    }

    /**
     * @Route("/about-us", name="aboutus")
     * @Template()
     */
    public function aboutUsAction()
    {
        return array();
    }
        
    /**
     * @Route("/api", name="api")
     * @Template()
     */
    public function apiAction()
    {
        return array();
    }


    
    /**
     * @Route("/search/all.{_format}", name="search_all", defaults={"_format" = "html"})
     * @Template()
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

        // Configure pagination
        $page = (int) $this->get('request')->get('page');
        $page = $page ?: 1;
        $page_info = array(
            'query'       => $query,
            'page_length' => $this->container->getParameter('search_page_length'),
            'page'        => $page,
            'offset'      => ($page - 1) * $this->container->getParameter('search_page_length'),
        );

        // Do the search
        $sphinx->setLimits($page_info['offset'], $page_info['page_length']);
        $appointees_results = $sphinx->Query($query, 'appointees');
        $companies_results  = $sphinx->Query($query, 'companies');

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
                'postcode'      => $a->getPostcode() ? substr($a->getPostcode(),0,strpos($a->getPostcode(), ' ')) : null,
          
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




      
        return array('appointees' => $appointees, 'companies' => $companies, 'query' => $query, 'appointee_companies' => $appointee_companies, 'company_appointees' => $company_appointees, 'page_info' => $page_info);
    }


    
    
    
}
