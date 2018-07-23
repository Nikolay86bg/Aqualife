<?php

/*
 * (c) 411 Marketing
 */

namespace App\Security\Voter;

use App\Entity\AccountEvent;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class TaskCompleteVoter.
 */
class TaskCompleteVoter extends Voter
{
    const COMPLETE = 'COMPLETE'.__CLASS__;

    /**
     * @param string       $attribute
     * @param AccountEvent $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        return (static::COMPLETE === $attribute) && ($subject instanceof Task);
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

        if ((!($user instanceof User))) {
            return false;
        }

        /* @var Task $subject */

        return $subject->isInEmployees($user);
    }
}
