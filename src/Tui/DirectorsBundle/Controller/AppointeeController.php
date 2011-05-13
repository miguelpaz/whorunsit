<?php

namespace Tui\DirectorsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Tui\DirectorsBundle\Entity\Appointee;
use Tui\DirectorsBundle\Entity\CompanyAppointment;

class AppointeeController extends Controller
{

    /**
     * @extra:Route("/appointees/{id}.{_format}", name="appointee_show", defaults={"_format" = "html"}, requirements={"_format" = "html|json|rdf"})
     * @extra:ParamConverter("id", class="TuiDirectorsBundle:Appointee")
     */
    public function showAppointeeAction(Appointee $appointee, $_format)
    {
        // Configure HTTP Cache
        $response = new Response();
        $response->setCache(array(
            // TODO: Use date of latest annotation, when annotations go live
            'last_modified' => new \DateTime($this->container->getParameter('ch_database_date')),
            'max_age'       => 86400, // 1 day - reduce with annotations
            's_maxage'      => 86400,
            'public'        => true,
        ));
        if ($response->isNotModified($this->get('request')))
        {
            return $response;
        }




        $trimmedPostcode = rtrim(substr($appointee->getPostcode(), 0, strpos($appointee->getPostcode(), ' ')));

        $em = $this->get('doctrine')->getEntityManager();   
        $r  = $em->getRepository('TuiDirectorsBundle:Appointee');
      
        // Get the total number of appointments
        $totalCompanyApps = $r->countAppointments($appointee);
      
        // Set the offset via page query
        $page = (int)$this->get('request')->query->get('page');     

        $offset = 0;
        $numAppointments = $this->container->getParameter('appointments_page_length');
        if ($page <= ceil($totalCompanyApps/$numAppointments) && $page > 1 )
        {
            $offset = ($page - 1) * $numAppointments;
        }
       
      
        // Get all the appointments for this page
        $companyAppointments = $r->getAppointments($appointee, $numAppointments, $offset);


      
        if ($_format == 'json')
        {
          
            $output = array(
                "id"                => $appointee->getId(),
                "is_corporate"      => $appointee->getIsCorporate(),
                "revision"          => $appointee->getRevision(),
                "postcode"          => $trimmedPostcode,
                "date_of_birth"     => $appointee->getDateofBirth() ? $appointee->getDateOfBirth()->format('Y-m') : NULL,
                "title"             => $appointee->getTitle(),
                "forenames"         => $appointee->getForenames(),
                "surname"           => $appointee->getSurname(),
                "honours"           => $appointee->getHonours(),
                "care_of"           => $appointee->getCareOf(),
                "country"           => $appointee->getCountry(),
                "occupation"        => $appointee->getOccupation(),
                "nationality"       => $appointee->getNationality(),
                "residence"         => $appointee->getResidence(),
                "url"               => $this->generateUrl('appointee_show', array('id' => $appointee->getId(), '_format' => 'json'), TRUE),
            );
          
            foreach ($companyAppointments as $appointment)
            {
                $company = $appointment->getCompany();
        
                $output["company_appointments"][] = array(
                    "company_id"    => $appointment->getCompanyId(),
                    "appointee_id"  => $appointment->getAppointeeId(),
                    "type"          => $appointment->getType(),
                    "appointed_on"  => $appointment->getAppointedOn() ? $appointment->getAppointedOn()->format('Y-m-d') : NULL,
                    "appointment_date_source"   => $appointment->getAppointmentDateSource(),
                    "resigned_on"   => $appointment->getResignedOn() ? $appointment->getResignedOn()->format('Y-m-d') : NULL,
                    "company"       => array(
                        "id"            => $company->getId(),
                        "name"          => $company->getName(),
                        "status"        => $company->getStatus(),
                        "officers"      => $company->getOfficers(),
                        "url"           => $this->generateUrl('company_show', array('id' => $company->getId(), '_format' => 'json'), TRUE),
                    ),
                );
            }
        
            $output["appointment_count"]      = $totalCompanyApps;
            $output["appointment_page_num"]   = $page;
            $output["appointment_page_count"] = (int)ceil($totalCompanyApps/$numAppointments);

        
            $response->setContent(json_encode($output));
            return $response;
      
        }

        $response->setContent($this->renderView("TuiDirectorsBundle:Appointee:showAppointee.$_format.twig", array(
            'appointee'                => $appointee,
            'trimmedPostcode'          => $trimmedPostcode,
            'companyAppointments'      => $companyAppointments,
            'totalCompanyAppointments' => $totalCompanyApps,
            'numAppointments'          => $numAppointments,
            'offset'                   => $offset,
            'page'                     => $page ?: 1,
        )));
        return $response;
    }
}
