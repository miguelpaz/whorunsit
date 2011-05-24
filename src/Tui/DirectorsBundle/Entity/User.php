<?php

namespace Tui\DirectorsBundle\Entity;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as orm;

/**
 * @orm\Entity
 * @orm\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @orm\Id
     * @orm\Column(type="integer")
     * @orm\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @orm\Column(type="boolean", name="contact_me", nullable=false)
     */
    protected $contact_me = false;
    

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
    
    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set contact_me
     *
     * @param boolean $contactMe
     */
    public function setContactMe($contactMe)
    {
        $this->contact_me = $contactMe;
    }

    /**
     * Get contact_me
     *
     * @return boolean $contactMe
     */
    public function getContactMe()
    {
        return $this->contact_me;
    }
}