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
     * @extra:Route("/search", name="search")
     * @extra:Template()
     */
  
    public function searchAction()
    {
        $query = $this->get('request')->get('q');
      
        $sphinx = $this->get('search');
        $appointees = $sphinx->Query($query, 'appointees');
        $companies = $sphinx->Query($query, 'companies');
        $results = array('appointees' => $appointees, 'companies' => $companies);
        die(var_dump($results));
      
        return array();
    }


    
    
    
}
