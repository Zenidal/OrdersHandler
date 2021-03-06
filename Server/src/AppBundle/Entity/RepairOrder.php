<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Form\Type\RepairOrderType;

/**
 * RepairOrder
 *
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RepairOrderRepository")
 * @ORM\Table(name="repair_order")
 */
class RepairOrder
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="Company")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;

    /**
     * @var Place
     *
     * @ORM\ManyToOne(targetEntity="Place")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="place_id", referencedColumnName="id")
     * })
     */
    private $place;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="engineer_id", referencedColumnName="id")
     * })
     */
    private $engineer;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $comment;

    /**
     * @var OrderHistory[]
     *
     * @ORM\OneToMany(targetEntity="OrderHistory", mappedBy="repairOrder", cascade={"all"})
     **/
    private $orderHistory;

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
        return $this->status;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
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
     * @return \AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set company
     *
     * @param \AppBundle\Entity\Company $company
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
     * @return \AppBundle\Entity\Company 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set place
     *
     * @param \AppBundle\Entity\Place $place
     * @return RepairOrder
     */
    public function setPlace(Place $place = null)
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

    /**
     * @return string
     */
    public function getTextStatus()
    {
       return RepairOrderType::getStatusByValue($this->status);
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return RepairOrder
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return RepairOrder
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set engineer
     *
     * @param \AppBundle\Entity\User $engineer
     * @return RepairOrder
     */
    public function setEngineer(\AppBundle\Entity\User $engineer = null)
    {
        $this->engineer = $engineer;

        return $this;
    }

    /**
     * Get engineer
     *
     * @return \AppBundle\Entity\User 
     */
    public function getEngineer()
    {
        return $this->engineer;
    }

    /**
     * Set comment
     *
     * @param \string $comment
     * @return RepairOrder
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return \string
     */
    public function getComment()
    {
        return $this->comment;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderHistory = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add orderHistory
     *
     * @param \AppBundle\Entity\OrderHistory $orderHistory
     * @return RepairOrder
     */
    public function addOrderHistory(\AppBundle\Entity\OrderHistory $orderHistory)
    {
        $this->orderHistory[] = $orderHistory;

        return $this;
    }

    /**
     * Remove orderHistory
     *
     * @param \AppBundle\Entity\OrderHistory $orderHistory
     */
    public function removeOrderHistory(\AppBundle\Entity\OrderHistory $orderHistory)
    {
        $this->orderHistory->removeElement($orderHistory);
    }

    /**
     * Get orderHistory
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrderHistory()
    {
        return $this->orderHistory;
    }
}
