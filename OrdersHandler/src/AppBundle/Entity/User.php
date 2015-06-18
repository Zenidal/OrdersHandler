<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 * @ORM\Entity
 * @UniqueEntity(
 *      "username",
 *      message="Username is already exists."
 * )
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var string $username
     * @Assert\NotBlank(
     *      message="Username can not be blank."
     * )
     * @Assert\Length(
     *      min = 6,
     *      max = 50,
     *      minMessage = "Username must be between 6 and 50 characters.",
     *      maxMessage = "Username must be between 6 and 50 characters."
     * )
     */
    private $username;

    /**
     * @var string
     * @Assert\NotBlank(
     *      message="Password can not be blank."
     * )
     * @Assert\Length(
     *      min = 6,
     *      max = 50,
     *      minMessage = "Password must be between 6 and 50 characters.",
     *      maxMessage = "Password must be between 6 and 50 characters."
     * )
     */
    private $password;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var string
     * @Assert\NotBlank(
     *      message="First name can not be blank."
     * )
     * @Assert\Length(
     *      min = 6,
     *      max = 50,
     *      minMessage = "First name must be between 6 and 50 characters.",
     *      maxMessage = "First name must be between 6 and 50 characters."
     * )
     */
    private $name;

    /**
     * @var string
     * @Assert\Length(
     *      min = 6,
     *      max = 50,
     *      minMessage = "Surname must be between 6 and 50 characters.",
     *      maxMessage = "Surname must be between 6 and 50 characters."
     * )
     */
    private $surname;

    /**
     * @var string
     * @Assert\NotBlank(
     *      message="E-mail can not be blank."
     * )
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     * @Assert\Length(
     *      min = 6,
     *      max = 50,
     *      minMessage = "E-mail must be between 6 and 50 characters.",
     *      maxMessage = "E-mail must be between 6 and 50 characters."
     * )
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
     * @var boolean
     */
    private $isActive = true;

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
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
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
     * @param RepairOrder $repairOrders
     * @return User
     */
    public function addRepairOrder(RepairOrder $repairOrders)
    {
        $this->repair_orders[] = $repairOrders;

        return $this;
    }

    /**
     * Remove repair_orders
     *
     * @param RepairOrder $repairOrders
     */
    public function removeRepairOrder(RepairOrder $repairOrders)
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
     * @param Role $role
     * @return User
     */
    public function setRole(Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    public function getRoles()
    {
        return array($this->getRole());
    }

    public function eraseCredentials()
    {
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }
}
