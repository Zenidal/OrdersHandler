<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Role;

class LoadRoleData implements FixtureInterface
{
    private $roles = [
        [
            'name' => 'Customer'
        ],
        [
            'name' => 'Manager'
        ],
        [
            'name' => 'Engineer'
        ]
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach($this->roles as $role){
            $newRole = new Role();
            $newRole->setName($role['name']);
            $manager->persist($newRole);
        }
        $manager->flush();
    }
}