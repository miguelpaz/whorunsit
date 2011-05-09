<?php

namespace Tui\DirectorsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Tui\DirectorsBundle\Entity\Company;

class CompanyController extends Controller
{

    /**
     * @extra:Route("/companies/{id}.{_format}", name="company_show", defaults={"_format" = "html"}, requirements={"_format" = "html|json|rdf"})
     * @extra:ParamConverter("id", class="TuiDirectorsBundle:Company")
     * @extra:Template()
     */
    public function showCompanyAction(Company $company, $_format)
    {
    
    	if ($_format == 'json')
    	{
    	
    		$output = array(
    			"id"		=> $company->getId(),
    			"status"	=> $company->getStatus(),
    			"officers"	=> $company->getOfficers(),
    			"name"		=> $company->getName(),
    			"url"		=> $this->generateUrl('company_show', array('id' => $company->getId(), '_format' => 'json'), TRUE),
    		);
    		
    		foreach($company->getCompanyAppointments() as $appointment)
    		{
    			$appointee = $appointment->getAppointee();
    			
    			$output["company_appointments"][] = array(
    				"company_id"	=> $appointment->getCompanyId(),
    				"appointee_id"	=> $appointment->getAppointeeId(),
    				"type"			=> $appointment->getType(),
    				"appointed_on"	=> $appointment->getAppointedOn() ? $appointment->getAppointedOn()->format('Y-m-d') : NULL,
      				"appointment_date_source"	=> $appointment->getAppointmentDateSource(),
      				"resigned_on"	=> $appointment->getResignedOn() ? $appointment->getResignedOn()->format('Y-m-d') : NULL,
      				"appointee"		=> array(
      					"id"			=> $appointee->getId(),
      					"is_corporate"	=> $appointee->getIsCorporate(),
      					"revision"		=> $appointee->getRevision(),
      					"postcode"		=> rtrim(substr($appointee->getPostcode(), 0, strpos($appointee->getPostcode(), ' '))),
      					"date_of_birth"	=> $appointee->getDateofBirth() ? $appointee->getDateOfBirth()->format('Y-m') : NULL,
      					"title"			=> $appointee->getTitle(),
      					"forenames"		=> $appointee->getForenames(),
      					"surname"		=> $appointee->getSurname(),
      					"honours"		=> $appointee->getHonours(),
      					"care_of"		=> $appointee->getCareOf(),
      					"country"		=> $appointee->getCountry(),
      					"occupation"	=> $appointee->getOccupation(),
      					"nationality"	=> $appointee->getNationality(),
      					"residence"		=> $appointee->getResidence(),
      					"url"			=> $this->generateUrl('appointee_show', array('id' => $appointee->getId(), '_format' => 'json'), TRUE),
      				),
    			);
    		}
    	
    	
    		return new Response(json_encode($output));
    	
    	}
    	
    	
    	
     	return array('company' => $company);
   
    }
}
