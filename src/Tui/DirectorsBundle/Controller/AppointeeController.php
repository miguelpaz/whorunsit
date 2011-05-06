<?php

namespace Tui\DirectorsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tui\DirectorsBundle\Entity\Appointee;
use Tui\DirectorsBundle\Entity\CompanyAppointment;

class AppointeeController extends Controller
{

    /**
     * @extra:Route("/appointees/{id}.{_format}", name="appointee_show", defaults={"_format" = "html"})
     * @extra:ParamConverter("id", class="TuiDirectorsBundle:Appointee")
     * @extra:Template()
     */
    public function showAppointeeAction(Appointee $appointee)
    {
      
      $trimmedPostcode = rtrim(substr($appointee->getPostcode(), 0, strpos($appointee->getPostcode(), ' ')));
            
      return array(
      	'appointee' => $appointee,
      	'trimmedPostcode' => $trimmedPostcode,
      	);
    
    }
}
