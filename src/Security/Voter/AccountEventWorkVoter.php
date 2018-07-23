<?php

/*
 * (c) 411 Marketing
 */

namespace App\Security\Voter;

use App\Entity\AccountEvent;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class AccountEventWorkVoter.
 */
class AccountEventWorkVoter extends Voter
{
    const WORK = 'WORK'.__CLASS__;

    /**
     * @param string       $attribute
     * @param AccountEvent $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        return (static::WORK === $attribute) && ($subject instanceof AccountEvent);
    }

    /**
     * @param string         $attribute
     * @param AccountEvent   $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if ((!($user instanceof User)) || (null === $subject->getAssignedTo())) {
            return false;
        }

        /* @var AccountEvent $subject */
        return $subject->getAssignedTo()->isEqualTo($user);
    }
}
