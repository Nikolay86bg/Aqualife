<?php

/*
 * (c) 411 Marketing
 */

namespace App\Security\Voter;

use App\Entity\Department;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PositionVoter extends Voter
{
    const POSITION_VIEW_ROLE = 'POSITION_VIEW_ROLE'.__CLASS__;
    const POSITION_EDIT_ROLE = 'POSITION_EDIT_ROLE'.__CLASS__;
    const POSITION_ADD_ROLE = 'POSITION_ADD_ROLE'.__CLASS__;
    const POSITION_LIST_ROLE = 'POSITION_LIST_ROLE'.__CLASS__;

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [
            self::POSITION_VIEW_ROLE,
            self::POSITION_EDIT_ROLE,
            self::POSITION_ADD_ROLE,
            self::POSITION_LIST_ROLE,
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
            case self::POSITION_LIST_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
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
            case self::POSITION_LIST_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) or
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) or
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
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
        switch ($attribute) {
            case self::POSITION_LIST_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) or
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) or
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
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
    public function getLBAccess(User $user, $attribute)
    {
        switch ($attribute) {
            case self::POSITION_LIST_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
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
        switch ($attribute) {
            case self::POSITION_LIST_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
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
    public function getContentWritingAccess(User $user, $attribute)
    {
        switch ($attribute) {
            case self::POSITION_LIST_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
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
    public function getTrainingAccess(User $user, $attribute)
    {
        switch ($attribute) {
            case self::POSITION_LIST_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
            default:
                return false;
        }
    }
}
