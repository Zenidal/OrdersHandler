<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class RoleRepository extends EntityRepository
{
    /**
     * @param string $name
     * @return mixed
     */
    public function findRoleByName($name)
    {
        $role = $this->createQueryBuilder('r')
            ->where('r.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
        return $role;
    }
}