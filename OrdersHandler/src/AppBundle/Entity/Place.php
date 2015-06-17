<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Place
 */
class Place
{
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
    private $name;

    /**
     * @var \AppBundle\Entity\Company
     */
    private $place;


    /**
     * Set name
     *
     * @param string $name
     * @return Place
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set place
     *
     * @param Company $place
     * @return Place
     */
    public function setPlace(Company $place = null)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return Company
     */
    public function getPlace()
    {
        return $this->place;
    }
    /**
     * @var Company
     */
    private $company;


    /**
     * Set company
     *
     * @param Company $company
     * @return Place
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $repairOrders;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->repairOrders = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add repairOrders
     *
     * @param \AppBundle\Entity\RepairOrder $repairOrders
     * @return Place
     */
    public function addRepairOrder(\AppBundle\Entity\RepairOrder $repairOrders)
    {
        $this->repairOrders[] = $repairOrders;

        return $this;
    }

    /**
     * Remove repairOrders
     *
     * @param \AppBundle\Entity\RepairOrder $repairOrders
     */
    public function removeRepairOrder(\AppBundle\Entity\RepairOrder $repairOrders)
    {
        $this->repairOrders->removeElement($repairOrders);
    }

    /**
     * Get repairOrders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRepairOrders()
    {
        return $this->repairOrders;
    }
}
