<?php

namespace Tui\DirectorsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Tui\DirectorsBundle\Entity\Company;

class CompanyController extends Controller
{

    /**
     * @Route("/companies/{id}.{_format}", name="company_show", defaults={"_format" = "html"}, requirements={"_format" = "html|json|rdf"})
     * @ParamConverter("id", class="TuiDirectorsBundle:Company")
     */
    public function showCompanyAction(Company $company, $_format)
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
        $response->headers->set('Link', '<http://whoruns.it/c/'.$company->getId().'>; rel=shorturl');
        if ($response->isNotModified($this->get('request')))
        {
            return $response;
        }
        
        
        
        $em = $this->getDoctrine();   
        $r  = $em->getRepository('TuiDirectorsBundle:Company');
              
        // Get the total number of appointments
        $totalCompanyApps = $r->countAppointments($company);
              
        // Set the offset via page query
        $page = (int)$this->get('request')->query->get('page');     
        $page = $page ?: 1;
        
        $offset = 0;
        $numAppointments = $this->container->getParameter('appointments_page_length');
        if ($page <= ceil($totalCompanyApps/$numAppointments) && $page > 1 )
        {
            $offset = ($page - 1) * $numAppointments;
        }
               
              
        // Get all the appointments for this page
        $companyAppointments = $r->getAppointments($company, $numAppointments, $offset);
        
        
    
        if ($_format == 'json')
        {
        
            $output = array(
                "id"        => $company->getId(),
                "status"    => $company->getStatus(),
                "officers"  => $company->getOfficers(),
                "name"      => $company->getName(),
                "url"       => $this->generateUrl('company_show', array('id' => $company->getId(), '_format' => 'json'), TRUE),
            );
            
            foreach($companyAppointments as $appointment)
            {
                $appointee = $appointment->getAppointee();
                
                $output["company_appointments"][] = array(
                    "company_id"    => $appointment->getCompanyId(),
                    "appointee_id"  => $appointment->getAppointeeId(),
                    "type"          => $appointment->getType(),
                    "appointed_on"  => $appointment->getAppointedOn() ? $appointment->getAppointedOn()->format('Y-m-d') : NULL,
                    "appointment_date_source"   => $appointment->getAppointmentDateSource(),
                    "resigned_on"   => $appointment->getResignedOn() ? $appointment->getResignedOn()->format('Y-m-d') : NULL,
                    "appointee"     => array(
                        "id"            => $appointee->getId(),
                        "is_corporate"  => $appointee->getIsCorporate(),
                        "revision"      => $appointee->getRevision(),
                        "postcode"      => rtrim(substr($appointee->getPostcode(), 0, strpos($appointee->getPostcode(), ' '))),
                        "date_of_birth" => $appointee->getDateofBirth() ? $appointee->getDateOfBirth()->format('Y-m') : NULL,
                        "title"         => $appointee->getTitle(),
                        "forenames"     => $appointee->getForenames(),
                        "surname"       => $appointee->getSurname(),
                        "honours"       => $appointee->getHonours(),
                        "care_of"       => $appointee->getCareOf(),
                        "country"       => $appointee->getCountry(),
                        "occupation"    => $appointee->getOccupation(),
                        "nationality"   => $appointee->getNationality(),
                        "residence"     => $appointee->getResidence(),
                        "url"           => $this->generateUrl('appointee_show', array('id' => $appointee->getId(), '_format' => 'json'), TRUE),
                    ),
                );
            }
        
            $output["appointment_count"]      = $totalCompanyApps;
            $output["appointment_page_num"]   = $page;
            $output["appointment_page_count"] = (int)ceil($totalCompanyApps/$numAppointments);
            
            // References
            $output['urls'] = array();
            $output['urls']['opencorporates'] = $this->jsonUrl('http://opencorporates.com/companies/gb/'.$company->getId().'.json', 'OpenCorporates', 'application/json');
            $output['urls']['companieshouse'] = $this->jsonUrl('http://data.companieshouse.gov.uk/doc/company/'.$company->getId().'.json', 'Companies House', 'application/json');
            if ($output['appointment_page_count'] > 1)
            {
                if ($page < $output['appointment_page_count'])
                {
                    $output['urls']['next_page'] = $this->jsonUrl(
                        $this->generateUrl('company_show', array('id' => $company->getId(), '_format' => 'json', 'page' => $page + 1)), 
                        'Appointments page '.($page+1), 
                        'application/json'
                    );
                }                

                if ($page > 1)
                {
                    $output['urls']['prev_page'] = $this->jsonUrl(
                        $this->generateUrl('company_show', array('id' => $company->getId(), '_format' => 'json', 'page' => $page - 1)), 
                        'Appointments page '.($page-1), 
                        'application/json'
                    );
                }                
            }

            $output['urls']['levelbusiness'] = $this->jsonUrl(
                'http://www.levelbusiness.com/doc/company/uk/'.$company->getId(), 
                'Level Business - accounts'
            );
        
        
        
            $response->setContent(json_encode($output));
            return $response;
        
        }
        
        
        $response->setContent($this->renderView("TuiDirectorsBundle:Company:showCompany.$_format.twig", array(
            'company'                  => $company,
            'companyAppointments'      => $companyAppointments,
            'totalCompanyAppointments' => $totalCompanyApps,
            'numAppointments'          => $numAppointments,
            'offset'                   => $offset,
            'page'                     => $page ?: 1,
        )));
        return $response;
   
    }
    
    
    public function jsonUrl($url, $title, $type = 'text/html')
    {
        return array(
            'href'  => $url,
            'title' => $title,
            'type'  => $type,
        );
    }
}
