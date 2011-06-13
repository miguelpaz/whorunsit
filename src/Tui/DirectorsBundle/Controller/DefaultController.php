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

        $query = trim(filter_var($this->get('request')->get('q'), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
        $page  = (int) $this->get('request')->get('page');
        $page  = $page ?: 1;
        
        if (!$query)
        {
            return $this->redirect($this->generateUrl('home'));
        }
        
        
        $search = $this->get('directorsearch');
        $results = $search->searchAll($query, $page);



        if ($this->get('request')->getRequestFormat() == 'json')
        {
            $out = $search->toJSON($results);

            if ($this->get('request')->get('callback',false))
            {
                $callback = filter_var($this->get('request')->get('callback'));
            
                return new Response($callback.'('.json_encode($out).')');
            }
          
            return new Response(json_encode($out));
        }
      
        return $results;
    }


    
    
    
}
