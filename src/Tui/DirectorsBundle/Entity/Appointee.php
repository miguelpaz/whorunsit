<?php

namespace Tui\DirectorsBundle\Entity;

/**
 * Tui\DirectorsBundle\Entity\Appointee
 *
 * @orm:Table(name="appointee")
 * @orm:Entity
 */
class Appointee
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
     * @var boolean $isCorporate
     *
     * @orm:Column(name="is_corporate", type="boolean", nullable=false)
     */
    private $isCorporate;

    /**
     * @var smallint $revision
     *
     * @orm:Column(name="revision", type="smallint", nullable=true)
     */
    private $revision;

    /**
     * @var string $postcode
     *
     * @orm:Column(name="postcode", type="string", length=8, nullable=true)
     */
    private $postcode;

    /**
     * @var date $dateOfBirth
     *
     * @orm:Column(name="date_of_birth", type="date", nullable=true)
     */
    private $dateOfBirth;

    /**
     * @var string $title
     *
     * @orm:Column(name="title", type="string", length=50, nullable=true)
     */
    private $title;

    /**
     * @var string $forenames
     *
     * @orm:Column(name="forenames", type="string", length=50, nullable=true)
     */
    private $forenames;

    /**
     * @var string $surname
     *
     * @orm:Column(name="surname", type="string", length=160, nullable=true)
     */
    private $surname;

    /**
     * @var string $honours
     *
     * @orm:Column(name="honours", type="string", length=50, nullable=true)
     */
    private $honours;

    /**
     * @var string $careOf
     *
     * @orm:Column(name="care_of", type="string", length=100, nullable=true)
     */
    private $careOf;

    /**
     * @var string $poBox
     *
     * @orm:Column(name="po_box", type="string", length=10, nullable=true)
     */
    private $poBox;

    /**
     * @var string $address1
     *
     * @orm:Column(name="address_1", type="string", length=50, nullable=true)
     */
    private $address1;

    /**
     * @var string $address2
     *
     * @orm:Column(name="address_2", type="string", length=50, nullable=true)
     */
    private $address2;

    /**
     * @var string $town
     *
     * @orm:Column(name="town", type="string", length=50, nullable=true)
     */
    private $town;

    /**
     * @var string $county
     *
     * @orm:Column(name="county", type="string", length=50, nullable=true)
     */
    private $county;

    /**
     * @var string $country
     *
     * @orm:Column(name="country", type="string", length=50, nullable=true)
     */
    private $country;

    /**
     * @var string $occupation
     *
     * @orm:Column(name="occupation", type="string", length=40, nullable=true)
     */
    private $occupation;

    /**
     * @var string $nationality
     *
     * @orm:Column(name="nationality", type="string", length=40, nullable=true)
     */
    private $nationality;

    /**
     * @var string $residence
     *
     * @orm:Column(name="residence", type="string", length=160, nullable=true)
     */
    private $residence;
    
    /**
     * @var CompanyAppointment
     *
     * @orm:OneToMany(targetEntity="CompanyAppointment", mappedBy="appointee")
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
     * Set isCorporate
     *
     * @param boolean $isCorporate
     */
    public function setIsCorporate($isCorporate)
    {
        $this->isCorporate = $isCorporate;
    }

    /**
     * Get isCorporate
     *
     * @return boolean $isCorporate
     */
    public function getIsCorporate()
    {
        return $this->isCorporate;
    }

    /**
     * Set revision
     *
     * @param smallint $revision
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;
    }

    /**
     * Get revision
     *
     * @return smallint $revision
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }

    /**
     * Get postcode
     *
     * @return string $postcode
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set dateOfBirth
     *
     * @param date $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * Get dateOfBirth
     *
     * @return date $dateOfBirth
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set forenames
     *
     * @param string $forenames
     */
    public function setForenames($forenames)
    {
        $this->forenames = $forenames;
    }

    /**
     * Get forenames
     *
     * @return string $forenames
     */
    public function getForenames()
    {
        return $this->forenames;
    }

    /**
     * Set surname
     *
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * Get surname
     *
     * @return string $surname
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set honours
     *
     * @param string $honours
     */
    public function setHonours($honours)
    {
        $this->honours = $honours;
    }

    /**
     * Get honours
     *
     * @return string $honours
     */
    public function getHonours()
    {
        return $this->honours;
    }

    /**
     * Set careOf
     *
     * @param string $careOf
     */
    public function setCareOf($careOf)
    {
        $this->careOf = $careOf;
    }

    /**
     * Get careOf
     *
     * @return string $careOf
     */
    public function getCareOf()
    {
        return $this->careOf;
    }

    /**
     * Set poBox
     *
     * @param string $poBox
     */
    public function setPoBox($poBox)
    {
        $this->poBox = $poBox;
    }

    /**
     * Get poBox
     *
     * @return string $poBox
     */
    public function getPoBox()
    {
        return $this->poBox;
    }

    /**
     * Set address1
     *
     * @param string $address1
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    }

    /**
     * Get address1
     *
     * @return string $address1
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    /**
     * Get address2
     *
     * @return string $address2
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set town
     *
     * @param string $town
     */
    public function setTown($town)
    {
        $this->town = $town;
    }

    /**
     * Get town
     *
     * @return string $town
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set county
     *
     * @param string $county
     */
    public function setCounty($county)
    {
        $this->county = $county;
    }

    /**
     * Get county
     *
     * @return string $county
     */
    public function getCounty()
    {
        return $this->county;
    }

    /**
     * Set country
     *
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Get country
     *
     * @return string $country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set occupation
     *
     * @param string $occupation
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;
    }

    /**
     * Get occupation
     *
     * @return string $occupation
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * Set nationality
     *
     * @param string $nationality
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    }

    /**
     * Get nationality
     *
     * @return string $nationality
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Set residence
     *
     * @param string $residence
     */
    public function setResidence($residence)
    {
        $this->residence = $residence;
    }

    /**
     * Get residence
     *
     * @return string $residence
     */
    public function getResidence()
    {
        return $this->residence;
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