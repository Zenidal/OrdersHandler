<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class RoleType extends AbstractType{

    const ROLE_CUSTOMER = 'Customer';
    const ROLE_MANAGER = 'Manager';
    const ROLE_ENGINEER = 'Engineer';

    public static function getRoleValues()
    {
        return [
            self::ROLE_CUSTOMER => 'Customer',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_ENGINEER => 'Engineer',
        ];
    }

    public function getName()
    {
        return 'role';
    }
}