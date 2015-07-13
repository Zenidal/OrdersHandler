<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Role',
            'csrf_protection' => false,
            'cascade_validation' => true,
            'validation_groups' => ['role']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Role'
        ));
    }

    public function getName()
    {
        return 'role';
    }
}