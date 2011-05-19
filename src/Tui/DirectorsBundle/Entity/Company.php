<?php

namespace Tui\DirectorsBundle\Entity;

/**
 * Tui\DirectorsBundle\Entity\Company
 *
 * @orm:Table(name="company")
 * @orm:Entity(repositoryClass="Tui\DirectorsBundle\Repositories\CompanyRepository")
 */
class Company
{
    /**
     * @var string $id
     *
     * @orm:Column(name="id", type="string", length=8, nullable=false)
     * @orm:Id
     * @orm:GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string $status
     *
     * @orm:Column(name="status", type="string", length=16, nullable=false)
     */
    private $status;

    /**
     * @var smallint $officers
     *
     * @orm:Column(name="officers", type="smallint", nullable=false)
     */
    private $officers;

    /**
     * @var string $name
     *
     * @orm:Column(name="name", type="string", length=161, nullable=false)
     */
    private $name;

	/**
     * @var CompanyAppointment     
     *
     * @orm:OneToMany(targetEntity="CompanyAppointment", mappedBy="company")
     */
    private $companyAppointments;


    /**
     * Set id
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return string $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set officers
     *
     * @param smallint $officers
     */
    public function setOfficers($officers)
    {
        $this->officers = $officers;
    }

    /**
     * Get officers
     *
     * @return smallint $officers
     */
    public function getOfficers()
    {
        return $this->officers;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }
    public function __construct()
    {
        $this->companyAppointments = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add companyAppointments
     *
     * @param Tui\DirectorsBundle\Entity\CompanyAppointment $companyAppointments
     */
    public function addCompanyAppointments(\Tui\DirectorsBundle\Entity\CompanyAppointment $companyAppointments)
    {
        $this->companyAppointments[] = $companyAppointments;
    }

    /**
     * Get companyAppointments
     *
     * @return Doctrine\Common\Collections\Collection $companyAppointments
     */
    public function getCompanyAppointments()
    {
        return $this->companyAppointments;
    }
}