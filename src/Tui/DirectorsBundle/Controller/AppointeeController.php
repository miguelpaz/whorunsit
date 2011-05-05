<?php

namespace Tui\DirectorsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tui\DirectorsBundle\Entity\Appointee;

class AppointeeController extends Controller
{

    /**
     * @extra:Route("/appointees/{id}", name="appointee_show")
     * @extra:ParamConverter("id", class="TuiDirectorsBundle:Appointee")
     * @extra:Template()
     */
    public function showAppointeeAction(Appointee $appointee)
    {
      return array('appointee' => $appointee);
    }
}
