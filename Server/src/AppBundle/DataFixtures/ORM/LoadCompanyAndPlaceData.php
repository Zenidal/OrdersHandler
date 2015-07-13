<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Company;
use AppBundle\Entity\Place;

class LoadCompanyAndPlaceData implements FixtureInterface {

    private $companies = [
        [
            'name' => 'iTransition Inc.',
            'places' => [
                ['name' => 'Place One Road Street'],
                ['name' => 'Place Two Mostovaya Street'],
                ['name' => 'Place Tree Sov.'],
            ]
        ],
        [
            'name' => 'Apple',
            'places' => [
                ['name' => 'Test 1'],
                ['name' => 'Test 2'],
                ['name' => 'Test 3'],
                ['name' => 'Test 4'],
            ]
        ],
        [
            'name' => 'Azot',
            'places' => [
                ['name' => 'Place 11'],
                ['name' => 'Place 22'],
                ['name' => 'Place 33'],
                ['name' => 'Place 44'],
            ]
        ],
        [
            'name' => 'SodaMoy Inc.',
            'places' => []
        ]
    ];

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->companies as $cat) {
            $company = new Company();
            $company->setName($cat['name']);

            foreach ($cat['places'] as $pl) {
                $place = new Place();
                $place->setName($pl['name']);
                $place->setCompany($company);

                $manager->persist($place);
            }

            $manager->persist($company);
            $manager->flush();
        }
    }
}