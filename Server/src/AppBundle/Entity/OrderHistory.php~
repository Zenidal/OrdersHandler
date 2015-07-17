<?php

namespace AppBundle\Entity;

use AppBundle\Form\Type\RepairOrderType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OrderHistory
 *
 * @ORM\Entity
 * @ORM\Table(name="orderHistory")
 */
class OrderHistory
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $establishedStatus;

    /**
     * @ORM\ManyToOne(targetEntity="RepairOrder", inversedBy="orderHistories")
     * @ORM\JoinColumn(name="repairOrder_id", referencedColumnName="id", onDelete="cascade")
     **/
    private $repairOrder;

    /**
     * @return string
     */
    public function getTextStatus()
    {
        return RepairOrderType::getStatusByValue($this->establishedStatus);
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
     * Set date
     *
     * @param \DateTime $date
     * @return OrderHistory
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set establishedStatus
     *
     * @param integer $establishedStatus
     * @return OrderHistory
     */
    public function setEstablishedStatus($establishedStatus)
    {
        $this->establishedStatus = $establishedStatus;

        return $this;
    }

    /**
     * Get establishedStatus
     *
     * @return integer 
     */
    public function getEstablishedStatus()
    {
        return $this->establishedStatus;
    }

    /**
     * Set repairOrder
     *
     * @param \AppBundle\Entity\RepairOrder $repairOrder
     * @return OrderHistory
     */
    public function setRepairOrder(\AppBundle\Entity\RepairOrder $repairOrder = null)
    {
        $this->repairOrder = $repairOrder;

        return $this;
    }

    /**
     * Get repairOrder
     *
     * @return \AppBundle\Entity\RepairOrder 
     */
    public function getRepairOrder()
    {
        return $this->repairOrder;
    }
}
