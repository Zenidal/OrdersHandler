<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class RoleType extends AbstractType{

    const ROLE_CUSTOMER = 'ROLE_CUSTOMER';
    const ROLE_MANAGER = 'ROLE_MANAGER';
    const ROLE_ENGINEER = 'ROLE_ENGINEER';

    public static function getRoleValues()
    {
        return [
            self::ROLE_CUSTOMER => 'ROLE_CUSTOMER',
            self::ROLE_MANAGER => 'ROLE_MANAGER',
            self::ROLE_ENGINEER => 'ROLE_ENGINEER',
        ];
    }

    public function getName()
    {
        return 'role';
    }
}