<?php
namespace Tui\DirectorsBundle\Entity;

use Doctrine\ORM\Mapping as orm;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Tui\DirectorsBundle\Entity\CompanyAppointment
 *
 * @orm\Table(name="company_appointment")
 * @orm\Entity(repositoryClass="Tui\DirectorsBundle\Repositories\CompanyRepository")
 */
class CompanyAppointment
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $companyId
     *
     * @orm\Column(name="company_id", type="string", length=8, nullable=false)
     */
    private $companyId;

    /**
     * @var string $appointeeId
     *
     * @orm\Column(name="appointee_id", type="string", length=8, nullable=false)
     */
    private $appointeeId;

    /**
     * @var string $type
     *
     * @orm\Column(name="type", type="string", length=35, nullable=false)
     */
    private $type;

    /**
     * @var date $appointedOn
     *
     * @orm\Column(name="appointed_on", type="date", nullable=false)
     */
    private $appointedOn;

    /**
     * @var string $appointmentDateSource
     *
     * @orm\Column(name="appointment_date_source", type="string", length=32, nullable=false)
     */
    private $appointmentDateSource;

    /**
     * @var date $resignedOn
     *
     * @orm\Column(name="resigned_on", type="date", nullable=true)
     */
    private $resignedOn;

    /**
     * @var Appointee
     *
     * @orm\ManyToOne(targetEntity="Appointee")
     * @orm\JoinColumns({
     *   @orm\JoinColumn(name="appointee_id", referencedColumnName="id")
     * })
     */
    private $appointee;

    /**
     * @var Company
     *
     * @orm\ManyToOne(targetEntity="Company")
     * @orm\JoinColumns({
     *   @orm\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;




    /**
     * Set id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set companyId
     *
     * @param string $companyId
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * Get companyId
     *
     * @return string 
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set appointeeId
     *
     * @param string $appointeeId
     */
    public function setAppointeeId($appointeeId)
    {
        $this->appointeeId = $appointeeId;
    }

    /**
     * Get appointeeId
     *
     * @return string 
     */
    public function getAppointeeId()
    {
        return $this->appointeeId;
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set appointedOn
     *
     * @param date $appointedOn
     */
    public function setAppointedOn($appointedOn)
    {
        $this->appointedOn = $appointedOn;
    }

    /**
     * Get appointedOn
     *
     * @return date 
     */
    public function getAppointedOn()
    {
        return $this->appointedOn;
    }

    /**
     * Set appointmentDateSource
     *
     * @param string $appointmentDateSource
     */
    public function setAppointmentDateSource($appointmentDateSource)
    {
        $this->appointmentDateSource = $appointmentDateSource;
    }

    /**
     * Get appointmentDateSource
     *
     * @return string 
     */
    public function getAppointmentDateSource()
    {
        return $this->appointmentDateSource;
    }

    /**
     * Set resignedOn
     *
     * @param date $resignedOn
     */
    public function setResignedOn($resignedOn)
    {
        $this->resignedOn = $resignedOn;
    }

    /**
     * Get resignedOn
     *
     * @return date 
     */
    public function getResignedOn()
    {
        return $this->resignedOn;
    }

    /**
     * Set appointee
     *
     * @param Tui\DirectorsBundle\Entity\Appointee $appointee
     */
    public function setAppointee(\Tui\DirectorsBundle\Entity\Appointee $appointee)
    {
        $this->appointee = $appointee;
    }

    /**
     * Get appointee
     *
     * @return Tui\DirectorsBundle\Entity\Appointee 
     */
    public function getAppointee()
    {
        return $this->appointee;
    }

    /**
     * Set company
     *
     * @param Tui\DirectorsBundle\Entity\Company $company
     */
    public function setCompany(\Tui\DirectorsBundle\Entity\Company $company)
    {
        $this->company = $company;
    }

    /**
     * Get company
     *
     * @return Tui\DirectorsBundle\Entity\Company 
     */
    public function getCompany()
    {
        return $this->company;
    }
}