<?php

namespace Tui\DirectorsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Tui\DirectorsBundle\Entity\Appointee;

class AppointeeController extends Controller
{

    /**
     * @extra:Route("/appointees/{id}.{_format}", name="appointee_show", defaults={"_format" = "html"}, requirements={"_format" = "html|json|rdf"})
     * @extra:ParamConverter("id", class="TuiDirectorsBundle:Appointee")
     * @extra:Template()
     */
    public function showAppointeeAction(Appointee $appointee, $_format)
    {
      
      $trimmedPostcode = rtrim(substr($appointee->getPostcode(), 0, strpos($appointee->getPostcode(), ' ')));
      
      if ($_format == 'json')
      {
      	
      	$output = array(
      		"id" 				=> $appointee->getId(),
      		"is_corporate" 		=> $appointee->getIsCorporate(),
      		"revision" 			=> $appointee->getRevision(),
      		"postcode" 			=> $trimmedPostcode,
      		"date_of_birth" 	=> $appointee->getDateofBirth() ? $appointee->getDateOfBirth()->format('Y-m') : NULL,
      		"title" 			=> $appointee->getTitle(),
      		"forenames" 		=> $appointee->getForenames(),
      		"surname" 			=> $appointee->getSurname(),
      		"honours" 			=> $appointee->getHonours(),
      		"care_of" 			=> $appointee->getCareOf(),
      		"country" 			=> $appointee->getCountry(),
      		"occupation" 		=> $appointee->getOccupation(),
      		"nationality" 		=> $appointee->getNationality(),
      		"residence" 		=> $appointee->getResidence(),
      		"url"				=> $this->generateUrl('appointee_show', array('id' => $appointee->getId(), '_format' => 'json'), TRUE),
      	);
  		
      	foreach ($appointee->getCompanyAppointments() as $appointment)
      	{
      		$company = $appointment->getCompany();
      	
      		$output["company_appointments"][] = array(
      			"company_id"	=> $appointment->getCompanyId(),
      			"appointee_id"	=> $appointment->getAppointeeId(),
      			"type"			=> $appointment->getType(),
      			"appointed_on"	=> $appointment->getAppointedOn() ? $appointment->getAppointedOn()->format('Y-m-d') : NULL,
      			"appointment_date_source"	=> $appointment->getAppointmentDateSource(),
      			"resigned_on"	=> $appointment->getResignedOn() ? $appointment->getResignedOn()->format('Y-m-d') : NULL,
      			"company"		=> array(
      				"id"			=> $company->getId(),
      				"name"			=> $company->getName(),
      				"status"		=> $company->getStatus(),
      				"officers"		=> $company->getOfficers(),
      				"url"			=> $this->generateUrl('company_show', array('id' => $company->getId(), '_format' => 'json'), TRUE),
      			),
      		);
      	}
      	
      	$output["appointment_count"] = count($output["company_appointments"]);
      	
      	return new Response(json_encode($output));
      
      }
      
           
      return array(
      	'appointee' => $appointee,
      	'trimmedPostcode' => $trimmedPostcode,
      );
    
    }
}
