<?php

/*
 * (c) 411 Marketing
 */

namespace App\Security\Voter;

use App\Entity\Department;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EvaluationVoter extends Voter
{
    const EVALUATION_VIEW_ROLE = 'EVALUATION_VIEW_ROLE'.__CLASS__;
    const EVALUATION_EDIT_ROLE = 'EVALUATION_EDIT_ROLE'.__CLASS__;
    const EVALUATION_ADD_ROLE = 'EVALUATION_ADD_ROLE'.__CLASS__;
    const EVALUATION_LIST_ROLE = 'EVALUATION_LIST_ROLE'.__CLASS__;
    const EVALUATION_PENDING_LIST_ROLE = 'EVALUATION_PENDING_LIST_ROLE'.__CLASS__;
    const EVALUATION_MANAGER_ROLE = 'EVALUATION_MANAGER_ROLE'.__CLASS__;
    const EVALUATION_EXPORT_ROLE = 'EVALUATION_EXPORT_ROLE'.__CLASS__;

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [
            self::EVALUATION_VIEW_ROLE,
            self::EVALUATION_EDIT_ROLE,
            self::EVALUATION_ADD_ROLE,
            self::EVALUATION_LIST_ROLE,
            self::EVALUATION_PENDING_LIST_ROLE,
            self::EVALUATION_MANAGER_ROLE,
            self::EVALUATION_EXPORT_ROLE,
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
            case self::EVALUATION_EDIT_ROLE:
                return !in_array(User::ROLE_USER, $user->getRoles(), true);
            case self::EVALUATION_EXPORT_ROLE:
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
    public function getAdvertisingAccess(User $user, $attribute)
    {
        switch ($attribute) {
            case self::EVALUATION_LIST_ROLE:
                return true;
            case self::EVALUATION_VIEW_ROLE:
                return true;
            case self::EVALUATION_MANAGER_ROLE:
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
            case self::EVALUATION_LIST_ROLE:
                return true;
            case self::EVALUATION_VIEW_ROLE:
                return true;
            case self::EVALUATION_MANAGER_ROLE:
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
            case self::EVALUATION_LIST_ROLE:
                return true;
            case self::EVALUATION_VIEW_ROLE:
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
    public function getWebDesignAccess(User $user, $attribute)
    {
        switch ($attribute) {
            case self::EVALUATION_LIST_ROLE:
                return true;
            case self::EVALUATION_VIEW_ROLE:
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
    public function getContentWritingAccess(User $user, $attribute)
    {
        if (in_array(User::ROLE_USER, $user->getRoles(), true)) {
            switch ($attribute) {
                case self::EVALUATION_LIST_ROLE:
                    return true;
                default:
                    return false;
            }
        }

        return true;
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
            case self::EVALUATION_LIST_ROLE:
                return true;
            case self::EVALUATION_VIEW_ROLE:
                return true;
            default:
                return false;
        }
    }
}
