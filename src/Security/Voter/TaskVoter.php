<?php

/*
 * (c) 411 Marketing
 */

namespace App\Security\Voter;

use App\Entity\Department;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    const TASK_CREATE_ROLE = 'TASK_CREATE_ROLE'.__CLASS__;
    const TASK_VIEW_ROLE = 'TASK_VIEW_ROLE'.__CLASS__;
    const TASK_EDIT_ROLE = 'TASK_EDIT_ROLE'.__CLASS__;

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [
            self::TASK_CREATE_ROLE,
            self::TASK_VIEW_ROLE,
            self::TASK_EDIT_ROLE,
        ], true)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        if (in_array(User::ROLE_ADMIN, $user->getRoles(), true)) {
            return true;
        }

        switch ($user->getDepartment()->getId()) {
            case Department::QUALITY_ASSURANCE_DEPARTMENT:
                return $this->getQAAccess($user, $attribute);
            case Department::ADVERTISING_DEPARTMENT:
                return $this->getAdvertisingAccess($user, $attribute);
            case Department::TECHNICAL_SUPPORT_DEPARTMENT:
                return $this->getTechnicalSupportAccess($user, $attribute);
            case Department::LINK_BUILDING_DEPARTMENT:
                return $this->getLBAccess($user, $attribute);
            case Department::WEB_DESIGN_DEPARTMENT:
                return $this->getWebDesignAccess($user, $attribute);
            case Department::CONTENT_WRITING_DEPARTMENT:
                return $this->getContentWritingAccess($user, $attribute);
            case Department::TRAINING_DEPARTMENT:
                return $this->getTrainingAccess($user, $attribute);
            case Department::REPORTING_DEPARTMENT:
                return $this->getQAAccess($user, $attribute);
            default:
                return false;
        }
    }

    /**
     * @param User $user
     * @param $attribute
     *
     * @return bool
     */
    public function getQAAccess(User $user, $attribute)
    {
        return false;
    }

    /**
     * @param User $user
     * @param $attribute
     *
     * @return bool
     */
    public function getAdvertisingAccess(User $user, $attribute)
    {
        switch ($attribute) {
            case self::TASK_VIEW_ROLE:
                return true;
            case self::TASK_CREATE_ROLE:
                return true;
            case self::TASK_EDIT_ROLE:
                if (!in_array(User::ROLE_USER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            default:
                return false;
        }
    }

    /**
     * @param User $user
     * @param $attribute
     *
     * @return bool
     */
    public function getTechnicalSupportAccess(User $user, $attribute)
    {
        return false;
    }

    /**
     * @param User $user
     * @param $attribute
     *
     * @return bool
     */
    public function getLBAccess(User $user, $attribute)
    {
        switch ($attribute) {
            case self::TASK_VIEW_ROLE:
                return true;
            case self::TASK_CREATE_ROLE:
                if (!in_array(User::ROLE_USER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::TASK_EDIT_ROLE:
                if (!in_array(User::ROLE_USER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            default:
                return false;
        }
    }

    /**
     * @param User $user
     * @param $attribute
     *
     * @return bool
     */
    public function getWebDesignAccess(User $user, $attribute)
    {
        return false;
    }

    /**
     * @param User $user
     * @param $attribute
     *
     * @return bool
     */
    public function getContentWritingAccess(User $user, $attribute)
    {
        return false;
    }

    /**
     * @param User $user
     * @param $attribute
     *
     * @return bool
     */
    public function getTrainingAccess(User $user, $attribute)
    {
        switch ($attribute) {
            case self::TASK_VIEW_ROLE:
                return true;
            case self::TASK_CREATE_ROLE:
                if (!in_array(User::ROLE_USER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            default:
                return false;
        }
    }
}
