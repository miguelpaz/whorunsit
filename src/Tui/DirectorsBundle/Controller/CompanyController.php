<?php

namespace Tui\DirectorsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tui\DirectorsBundle\Entity\Company;

class CompanyController extends Controller
{

    /**
     * @extra:Route("/company/{id}", name="company_show")
     * @extra:ParamConverter("id", class="TuiDirectorsBundle:Company")
     * @extra:Template()
     */
    public function showCompanyAction(Company $company)
    {
      return array('company' => $company);
    }
}
