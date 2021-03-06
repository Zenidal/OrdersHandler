<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\RepairOrder;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface, AdvancedUserInterface, \Serializable
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
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     * @var string
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $email;

    /**
     * @var repairOrder[]
     *
     * @ORM\OneToMany(targetEntity="RepairOrder", mappedBy="user", cascade={"remove"})
     */
    private $repairOrders;

    /**
     * @var repairOrder[]
     *
     * @ORM\OneToMany(targetEntity="RepairOrder", mappedBy="user", cascade={"remove"})
     */
    private $assignedRepairOrders;

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
    private $isActive;

    /**
     * @ORM\Column(type="text")
     */
    private $confirmationLink;

    /**
     * @ORM\ManyToMany(targetEntity="Company", inversedBy="users")
     * @ORM\JoinTable(name="users_companies")
     **/
    private $companies;

    /**
     * @ORM\OneToOne(targetEntity="Token", inversedBy="user")
     * @ORM\JoinColumn(name="token_id", referencedColumnName="id", onDelete="cascade")
     **/
    private $token;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->companies = new ArrayCollection();
        $this->repairOrders = new ArrayCollection();
        $this->isActive = false;
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
        return [$this->role];
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

    /**
     * Add assignedRepairOrders
     *
     * @param \AppBundle\Entity\RepairOrder $assignedRepairOrders
     * @return User
     */
    public function addAssignedRepairOrder(\AppBundle\Entity\RepairOrder $assignedRepairOrders)
    {
        $this->assignedRepairOrders[] = $assignedRepairOrders;

        return $this;
    }

    /**
     * Remove assignedRepairOrders
     *
     * @param RepairOrder[] $assignedRepairOrders
     */
    public function removeAssignedRepairOrder(RepairOrder $assignedRepairOrders)
    {
        $this->assignedRepairOrders->removeElement($assignedRepairOrders);
    }

    /**
     * Get assignedRepairOrders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssignedRepairOrders()
    {
        return $this->assignedRepairOrders;
    }

    /**
     * Set confirmationLink
     *
     * @param string $confirmationLink
     * @return User
     */
    public function setConfirmationLink($confirmationLink)
    {
        $this->confirmationLink = $confirmationLink;

        return $this;
    }

    /**
     * Get confirmationLink
     *
     * @return string 
     */
    public function getConfirmationLink()
    {
        return $this->confirmationLink;
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->isActive;
    }

    /**
     * Set token
     *
     * @param \AppBundle\Entity\Token $token
     * @return User
     */
    public function setToken(\AppBundle\Entity\Token $token = null)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return \AppBundle\Entity\Token 
     */
    public function getToken()
    {
        return $this->token;
    }
}
