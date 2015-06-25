<?php

namespace AppBundle\Entity;

use AppBundle\Repository\RoleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(
 *      "username",
 *      message="Username is already exists."
 * )
 */
class User implements UserInterface, \Serializable
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=100)
     * @var string
     * @Assert\NotBlank(
     *      message="First name can not be blank."
     * )
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "First name must be between 2 and 50 characters.",
     *      maxMessage = "First name must be between 2 and 50 characters."
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     * @var string
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Surname must be between 2 and 50 characters.",
     *      maxMessage = "Surname must be between 2 and 50 characters."
     * )
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @var repairOrder[]
     *
     * @ORM\OneToMany(targetEntity="RepairOrder", mappedBy="user", cascade={"remove"})
     */
    private $repairOrders;
    
    /**
    * @var Role
    *
    * @ORM\ManyToOne(targetEntity="Role")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="role_id", referencedColumnName="id")
    * })
    */
    private $role;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive = true;

    /**
     * @var Company[]
     *
     * @ORM\ManyToMany(targetEntity="Company", mappedBy="users", cascade={"persist"})
     */
    private $companies;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->companies = new ArrayCollection();
        $this->repairOrders = new ArrayCollection();
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
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
     * Set firstName
     *
     * @param string $name
     * @return User
     */
    public function setFirstName($name)
    {
        $this->firstName = $name;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
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
        $this->repairOrders[] = $repairOrders;

        return $this;
    }

    /**
     * Remove repair_orders
     *
     * @param RepairOrder $repairOrders
     */
    public function removeRepairOrder(RepairOrder $repairOrders)
    {
        $this->repairOrders->removeElement($repairOrders);
    }

    /**
     * Get repair_orders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRepairOrders()
    {
        return $this->repairOrders;
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

    public function getRoleName()
    {
        return $this->getRole()->getName();
    }

    public function getRoles()
    {
        return array('ROLE_USER');
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

    /**
     * Add companies
     *
     * @param \AppBundle\Entity\Company $companies
     * @return User
     */
    public function addCompany(Company $companies = null)
    {
        $this->companies[] = $companies;

        return $this;
    }

    /**
     * Remove company
     *
     * @param Company $company
     */
    public function removeCompany(Company $company)
    {
        $this->companies->removeElement($company);
    }

    /**
     * Get companies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompanies()
    {
        return $this->companies;
    }
}
