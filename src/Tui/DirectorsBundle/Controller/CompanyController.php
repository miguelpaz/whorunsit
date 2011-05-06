<?php

namespace Tui\DirectorsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tui\DirectorsBundle\Entity\Company;

class CompanyController extends Controller
{

    /**
     * @extra:Route("/companies/{id}.{_format}", name="company_show", defaults={"_format" = "html"})
     * @extra:ParamConverter("id", class="TuiDirectorsBundle:Company")
     * @extra:Template()
     */
    public function showCompanyAction(Company $company)
    {
    	
     	return array('company' => $company);
   
    }
}
