<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Form\RepairOrderType;

/**
 * RepairOrder
 */
class RepairOrder
{
    private static function getStatusByValue($value)
    {
        switch ($value)
        {
            case 1:
                return "Open";
                break;
            case 2:
                return "Assigned";
                break;
            case 3:
                return "In process";
                break;
            case 4:
                return "Resolved";
                break;
            case 5:
                return "Closed";
                break;
            case 6:
                return "Reopened";
                break;
            default:
                return "Unknown status";
        }
    }

    /**
     * @var integer
     */
    private $id;

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
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $address;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var \AppBundle\Entity\User
     */
    private $user;

    /**
     * @var \AppBundle\Entity\Company
     */
    private $company;


    /**
     * Set description
     *
     * @param string $description
     * @return RepairOrder
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return RepairOrder
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return RepairOrder
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return RepairOrder::getStatusByValue($this->status);
    }

    /**
     * Set user
     *
     * @param User $user
     * @return RepairOrder
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set company
     *
     * @param Company $company
     * @return RepairOrder
     */
    public function setCompany(Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @var \AppBundle\Entity\Place
     */
    private $place;


    /**
     * Set place
     *
     * @param \AppBundle\Entity\Place $place
     * @return RepairOrder
     */
    public function setPlace(\AppBundle\Entity\place $place = null)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return \AppBundle\Entity\Place
     */
    public function getPlace()
    {
        return $this->place;
    }
}
