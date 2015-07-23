<?php
namespace AppBundle\Security\Authorization\Voter;

use AppBundle\Form\Type\RepairOrderType;
use AppBundle\Form\Type\RoleType;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class RepairOrderVoter implements VoterInterface
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const CREATE = 'create';
    const DELETE = 'delete';
    const ASSIGN = 'assign';
    const START = 'start';
    const FINISH = 'finish';
    const CLOSE = 'close';
    const REOPEN = 'reopen';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::VIEW,
            self::EDIT,
            self::CREATE,
            self::DELETE,
            self::ASSIGN,
            self::START,
            self::FINISH,
            self::CLOSE,
            self::REOPEN
        ));
    }

    public function supportsClass($class)
    {
        $supportedClass = 'AppBundle\Entity\RepairOrder';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @var \AppBundle\Entity\RepairOrder $repairOrder
     */
    public function vote(TokenInterface $token, $repairOrder, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($repairOrder))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // check if the voter is used correct, only allow one attribute
        // this isn't a requirement, it's just one easy way for you to
        // design your voter
        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException(
                'Only one attribute is allowed for VIEW or EDIT'
            );
        }

        // set the attribute to check against
        $attribute = $attributes[0];

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $user = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch ($attribute) {
            case self::VIEW:
                // the data object could have for example a method isPrivate()
                // which checks the boolean attribute $private
                if ($user->getRole()->getName() === RoleType::ROLE_MANAGER) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                if ($repairOrder->getUser() === $user) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                if (!is_null($repairOrder->getEngineer()) && $repairOrder->getEngineer()->getId() === $user->getId() && $user->getRole()->getName() === RoleType::ROLE_ENGINEER) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::EDIT:
                // we assume that our data object has a method getOwner() to
                // get the current owner user entity for this data object
                if ($user->getRole()->getName() === RoleType::ROLE_MANAGER ||
                    (
                        $repairOrder->getUser() === $user && $repairOrder->getStatus() === RepairOrderType::STATUS_OPEN ||
                        $repairOrder->getUser() === $user && $repairOrder->getStatus() === RepairOrderType::STATUS_REOPENED
                    )
                ) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::CREATE:
                if (in_array($user->getRole()->getName(), [RoleType::ROLE_CUSTOMER])) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::DELETE:
                if ($user->getRole()->getName() === RoleType::ROLE_MANAGER ||
                    (
                        $repairOrder->getUser() === $user && $repairOrder->getStatus() === RepairOrderType::STATUS_OPEN ||
                        $repairOrder->getUser() === $user && $repairOrder->getStatus() === RepairOrderType::STATUS_REOPENED
                    )
                ) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::ASSIGN:
                if (!in_array($repairOrder->getStatus(), [RepairOrderType::STATUS_IN_PROCESS, RepairOrderType::STATUS_CLOSED]) &&
                    $user->getRole()->getName() === RoleType::ROLE_MANAGER
                ) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::START:
                if (in_array($repairOrder->getStatus(), [RepairOrderType::STATUS_ASSIGNED, RepairOrderType::STATUS_OPEN, RepairOrderType::STATUS_REOPENED]) &&
                    !is_null($repairOrder->getEngineer())
                ) {
                    if (
                        $repairOrder->getEngineer()->getId() === $user->getId() &&
                        $user->getRole()->getName() === RoleType::ROLE_ENGINEER
                    ) {
                        return VoterInterface::ACCESS_GRANTED;
                    }
                }
                break;

            case self::FINISH:
                if (in_array($repairOrder->getStatus(), [RepairOrderType::STATUS_IN_PROCESS]) &&
                    $repairOrder->getEngineer()->getId() === $user->getId() &&
                    $user->getRole()->getName() === RoleType::ROLE_ENGINEER
                ) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::CLOSE:
                if (in_array($repairOrder->getStatus(), [RepairOrderType::STATUS_RESOLVED]) &&
                    (
                        (
                            $repairOrder->getUser()->getId() === $user->getId() &&
                            $user->getRole()->getName() === RoleType::ROLE_CUSTOMER
                        ) ||
                        $user->getRole()->getName() === RoleType::ROLE_MANAGER
                    )

                ) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::REOPEN:
                if (in_array($repairOrder->getStatus(), [RepairOrderType::STATUS_RESOLVED]) &&
                    (
                        (
                            $repairOrder->getUser()->getId() === $user->getId() &&
                            $user->getRole()->getName() === RoleType::ROLE_CUSTOMER
                        ) ||
                        $user->getRole()->getName() === RoleType::ROLE_MANAGER
                    )

                ) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}