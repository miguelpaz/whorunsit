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
}
