<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;

class LoadRoleAndUserData implements FixtureInterface
{
    private $roles = [
        [
            'name' => 'ROLE_CUSTOMER',
            'users' => []
        ],
        [
            'name' => 'ROLE_MANAGER',
            'users' => [
                [
                    'username' => 'Admin',
                    'password' => '$2y$13$07134d87462ae3b43a1aaO.nl6OtPJBQIUIxmFII3VLDGX4lHMYh.',
                    'salt' => '07134d87462ae3b43a1aac5b6b232739',
                    'first_name' => 'Alexandr',
                    'surname' => 'Miskevich',
                    'email' => '1ochka1994@gmail.com'
                ]
            ]
        ],
        [
            'name' => 'ROLE_ENGINEER',
            'users' => []
        ]
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->roles as $role) {
            $newRole = new Role();
            $newRole->setName($role['name']);
            foreach ($role['users'] as $user) {
                $newUser = new User();
                $newUser->setUsername($user['username']);
                $newUser->setPassword($user['password']);
                $newUser->setSalt($user['salt']);
                $newUser->setFirstName($user['first_name']);
                $newUser->setSurname($user['surname']);
                $newUser->setEmail($user['email']);
                $newUser->setRole($newRole);
                $manager->persist($newUser);
            }

            $manager->persist($newRole);
        }
        $manager->flush();
    }
}