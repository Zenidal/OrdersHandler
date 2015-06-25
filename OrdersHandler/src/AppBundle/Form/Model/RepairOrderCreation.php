<?php
namespace AppBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use AppBundle\Entity\RepairOrder;

class RepairOrderCreation
{
    /**
     * @Assert\Type(type="AppBundle\Entity\RepairOrder")
     * @Assert\Valid()
     */
    protected $repairOrder;



    public function setRepairOrder(RepairOrder $repairOrder)
    {
        $this->repairOrder = $repairOrder;
    }

    public function getRepairOrder()
    {
        return $this->repairOrder;
    }
}