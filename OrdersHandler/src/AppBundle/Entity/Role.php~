<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Role
 *
 * @ORM\Entity
 * @ORM\Table(name="role")
 */
class Role implements RoleInterface
{
    const ROLE_CUSTOMER = 'Customer';
    const ROLE_MANAGER = 'Manager';
    const ROLE_ENGINEER = 'Engineer';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var User[]
     *
     * @ORM\OneToMany(targetEntity="User", mappedBy="user", cascade={"remove"})
     */
    private $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Role
     */
    public function setName($name)
    {
        if (!in_array($name, array(self::ROLE_CUSTOMER, self::ROLE_MANAGER, self::ROLE_ENGINEER))) {
            throw new \InvalidArgumentException("Invalid role name");
        }
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add users
     *
     * @param \AppBundle\Entity\User $users
     * @return Role
     */
    public function addUser(User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \AppBundle\Entity\User $users
     */
    public function removeUser(User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * by RoleInterface.
     *
     * @return string The role.
     */
    public function getRole()
    {
        return $this->getName();
    }
}
