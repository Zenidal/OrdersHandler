<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 */
class User
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password_hash;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $surname;

    /**
     * @var string
     */
    private $email;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $repair_orders;

    /**
     * @var \AppBundle\Entity\Role
     */
    private $role;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->repair_orders = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password_hash
     *
     * @param string $passwordHash
     * @return User
     */
    public function setPasswordHash($passwordHash)
    {
        $this->password_hash = $passwordHash;

        return $this;
    }

    /**
     * Get password_hash
     *
     * @return string 
     */
    public function getPasswordHash()
    {
        return $this->password_hash;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
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
     * Set surname
     *
     * @param string $surname
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string 
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
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
     * Add repair_orders
     *
     * @param \AppBundle\Entity\RepairOrder $repairOrders
     * @return User
     */
    public function addRepairOrder(\AppBundle\Entity\RepairOrder $repairOrders)
    {
        $this->repair_orders[] = $repairOrders;

        return $this;
    }

    /**
     * Remove repair_orders
     *
     * @param \AppBundle\Entity\RepairOrder $repairOrders
     */
    public function removeRepairOrder(\AppBundle\Entity\RepairOrder $repairOrders)
    {
        $this->repair_orders->removeElement($repairOrders);
    }

    /**
     * Get repair_orders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRepairOrders()
    {
        return $this->repair_orders;
    }

    /**
     * Set role
     *
     * @param \AppBundle\Entity\Role $role
     * @return User
     */
    public function setRole(\AppBundle\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \AppBundle\Entity\Role 
     */
    public function getRole()
    {
        return $this->role;
    }
}
