<?php

/*
 * (c) 411 Marketing
 */

namespace App\Security\Voter;

use App\Entity\Department;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccountEventsVoter extends Voter
{
    const ACCOUNT_EVENTS_VIEW_ROLE = 'ACCOUNT_EVENTS_VIEW_ROLE'.__CLASS__;
    const ACCOUNT_EVENTS_EDIT_ROLE = 'ACCOUNT_EVENTS_EDIT_ROLE'.__CLASS__;
    const ACCOUNT_EVENTS_ADD_ROLE = 'ACCOUNT_EVENTS_ADD_ROLE'.__CLASS__;
    const ACCOUNT_EVENTS_LIST_ROLE = 'ACCOUNT_EVENTS_LIST_ROLE'.__CLASS__;
    const ACCOUNT_EVENTS_BULK_EDIT_ROLE = 'ACCOUNT_EVENTS_BULK_EDIT_ROLE'.__CLASS__;
    const ACCOUNT_EVENTS_DIRECT_ROLE = 'ACCOUNT_EVENTS_DIRECT_ROLE'.__CLASS__;
    const ACCOUNT_EVENTS_COMPLETED_NO_EVAL_ROLE = 'ACCOUNT_EVENTS_COMPLETED_NO_EVAL_ROLE'.__CLASS__;
    const ACCOUNT_EVENTS_COMPLETED_NO_EVAL_BULK_EDIT_ROLE = 'ACCOUNT_EVENTS_COMPLETED_NO_EVAL_BULK_EDIT_ROLE'.__CLASS__;
    const ACCOUNT_EVENTS_GET_INFO_FROM_CRM = 'ACCOUNT_EVENTS_GET_INFO_FROM_CRM'.__CLASS__;

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [
            self::ACCOUNT_EVENTS_VIEW_ROLE,
            self::ACCOUNT_EVENTS_EDIT_ROLE,
            self::ACCOUNT_EVENTS_ADD_ROLE,
            self::ACCOUNT_EVENTS_LIST_ROLE,
            self::ACCOUNT_EVENTS_BULK_EDIT_ROLE,
            self::ACCOUNT_EVENTS_DIRECT_ROLE,
            self::ACCOUNT_EVENTS_COMPLETED_NO_EVAL_ROLE,
            self::ACCOUNT_EVENTS_COMPLETED_NO_EVAL_BULK_EDIT_ROLE,
            self::ACCOUNT_EVENTS_GET_INFO_FROM_CRM,
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
        switch ($attribute) {
            case self::ACCOUNT_EVENTS_LIST_ROLE:
                return !in_array(User::ROLE_USER, $user->getRoles(), true);
            case self::ACCOUNT_EVENTS_DIRECT_ROLE:
                return true;
            case self::ACCOUNT_EVENTS_COMPLETED_NO_EVAL_ROLE:
                return !in_array(User::ROLE_USER, $user->getRoles(), true);
            case self::ACCOUNT_EVENTS_COMPLETED_NO_EVAL_BULK_EDIT_ROLE:
                return !in_array(User::ROLE_USER, $user->getRoles(), true);
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
    public function getAdvertisingAccess(User $user, $attribute)
    {
        switch ($attribute) {
            case self::ACCOUNT_EVENTS_LIST_ROLE:
                return true;
            case self::ACCOUNT_EVENTS_BULK_EDIT_ROLE:
                return !in_array(User::ROLE_USER, $user->getRoles(), true);
            case self::ACCOUNT_EVENTS_EDIT_ROLE:
                return !in_array(User::ROLE_USER, $user->getRoles(), true) &&
                    !in_array(User::ROLE_TL, $user->getRoles(), true);
            case self::ACCOUNT_EVENTS_DIRECT_ROLE:
                return false;
            case self::ACCOUNT_EVENTS_VIEW_ROLE:
                return true;
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
        switch ($attribute) {
            case self::ACCOUNT_EVENTS_LIST_ROLE:
                return true;
            case self::ACCOUNT_EVENTS_BULK_EDIT_ROLE:
                return !in_array(User::ROLE_USER, $user->getRoles(), true);
            case self::ACCOUNT_EVENTS_EDIT_ROLE:
                return !in_array(User::ROLE_USER, $user->getRoles(), true) &&
                    !in_array(User::ROLE_TL, $user->getRoles(), true);
            case self::ACCOUNT_EVENTS_DIRECT_ROLE:
                return false;
            case self::ACCOUNT_EVENTS_VIEW_ROLE:
                return true;
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
    public function getLBAccess(User $user, $attribute)
    {
        switch ($attribute) {
            case self::ACCOUNT_EVENTS_BULK_EDIT_ROLE:
                return true;
            case self::ACCOUNT_EVENTS_DIRECT_ROLE:
                return false;
            case self::ACCOUNT_EVENTS_EDIT_ROLE:
                return !in_array(User::ROLE_USER, $user->getRoles(), true);
            case self::ACCOUNT_EVENTS_GET_INFO_FROM_CRM:
                return true;
            default:
                return true;
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
        switch ($attribute) {
            case self::ACCOUNT_EVENTS_BULK_EDIT_ROLE:
                return !in_array(User::ROLE_USER, $user->getRoles(), true);
            case self::ACCOUNT_EVENTS_DIRECT_ROLE:
                return false;
            case self::ACCOUNT_EVENTS_EDIT_ROLE:
                return false;
            case self::ACCOUNT_EVENTS_GET_INFO_FROM_CRM:
                return false;
            default:
                return true;
        }
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
            case self::ACCOUNT_EVENTS_BULK_EDIT_ROLE:
                return !in_array(User::ROLE_USER, $user->getRoles(), true);
            case self::ACCOUNT_EVENTS_DIRECT_ROLE:
                return false;
            case self::ACCOUNT_EVENTS_EDIT_ROLE:
                return !in_array(User::ROLE_USER, $user->getRoles(), true);
            case self::ACCOUNT_EVENTS_GET_INFO_FROM_CRM:
                return true;
            default:
                return true;
        }
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
}
