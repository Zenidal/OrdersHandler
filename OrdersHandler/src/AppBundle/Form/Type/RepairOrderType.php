<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\RepairOrder;
use AppBundle\Form\Type\CompanyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Repository\CompanyRepository;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class RepairOrderType extends AbstractType
{
    const STATUS_OPEN = 1;
    const STATUS_ASSIGNED = 2;
    const STATUS_IN_PROCESS = 3;
    const STATUS_RESOLVED = 4;
    const STATUS_CLOSED = 5;
    const STATUS_REOPENED = 6;

    private $tokenStorage;

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

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param integer $value
     * @return string
     */
    public static function getStatusByValue($value)
    {
        $statuses = self::getStatusValues();
        return isset($statuses[$value]) ? $statuses[$value] : '';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $builder
            ->add('description')
            ->add('address')
            ->add('submit', 'submit')
            ->add('company', 'entity', [
                'class' => 'AppBundle:Company',
                'property' => 'name',
                'query_builder' => function (CompanyRepository $companyRepository) use ($user) {
                    $qb = $companyRepository->createQueryBuilder('company');
                    return $qb
                        ->add('where', $qb->expr()->in('company', ':companies'))
                        ->setParameter('companies', $user->getCompanies()->toArray());
                },
            ])
            ->add('place', 'entity', [
                'class' => 'AppBundle:Place',
                'property' => 'name',
            ])
            ->add('company', 'collection', [
                'type' => new CompanyType(),
            ]);
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
            'validation_groups' => ['repairOrder']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\RepairOrder'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'repairOrder';
    }
}