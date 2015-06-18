<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class RepairOrderType extends AbstractType
{
    const STATUS_OPEN = 1;
    const STATUS_ASSIGNED = 2;
    const STATUS_IN_PROCESS = 3;
    const STATUS_RESOLVED = 4;
    const STATUS_CLOSED = 5;
    const STATUS_REOPENED = 6;

    /**
     * @return array
     */
    public static function getStatusValues()
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_IN_PROCESS => 'In process',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_REOPENED => 'Reopened'
        ];
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('address')
            ->add('submit', 'submit')
            ->add('company', 'entity', array(
                'class' => 'AppBundle:Company',
                'property' => 'name',
            ))
            ->add('place', 'entity', array(
                'class' => 'AppBundle:Place',
                'property' => 'name',
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\RepairOrder',
            'csrf_protection' => false,
            'cascade_validation' => true,
            'validation_groups' => ['repairOrder'],
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'repairOrder';
    }
}