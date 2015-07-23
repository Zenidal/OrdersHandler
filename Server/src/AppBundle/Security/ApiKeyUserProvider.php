<?php
namespace AppBundle\Security;

use AppBundle\Entity\Token;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiKeyUserProvider implements UserProviderInterface
{
    /** @var EntityManager $em */
    private $em;

    public function __construct($entity_manager)
    {
        $this->em = $entity_manager;
    }

    public function getUsernameForApiKey($apiKey)
    {
        /** @var Token $token */
        $token = $this->em->getRepository('AppBundle\Entity\Token')->findOneBy([
            'value' => $apiKey
        ]);
        if ($token) {
            return $token->getUser()->getUsername();
        }

        return null;
    }

    public function loadUserByUsername($username)
    {
        return new User(
            $username,
            null,
            array('ROLE_USER')
        );
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return 'Symfony\Component\Security\Core\User\User' === $class;
    }
}