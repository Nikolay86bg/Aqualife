<?php

/*
 * (c) 411 Marketing
 */

namespace App\Security\Voter;

use App\Entity\Department;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ReportVoter extends Voter
{
    const REPORT_LB_ROLE = 'REPORT_LB_ROLE'.__CLASS__;
    const REPORT_TENURED_LB_ROLE = 'REPORT_TENURED_LB_ROLE'.__CLASS__;
    const REPORT_DELAYED_ROLE = 'REPORT_DELAYED_ROLE'.__CLASS__;
    const REPORT_QA_WEBSITE_ROLE = 'REPORT_QA_WEBSITE_ROLE'.__CLASS__;
    const REPORT_DAILY_OUTPUT_ROLE = 'REPORT_DAILY_OUTPUT_ROLE'.__CLASS__;
    const REPORT_TECH_SCORE_ROLE = 'REPORT_TECH_SCORE_ROLE'.__CLASS__;
    const REPORT_QNQ_ROLE = 'REPORT_QNQ_ROLE'.__CLASS__;
    const REPORT_TECH_QUANTITY_ROLE = 'REPORT_TECH_QUANTITY_ROLE'.__CLASS__;
    const REPORT_REPLACEMENT_DIR_ROLE = 'REPORT_REPLACEMENT_DIR_ROLE'.__CLASS__;
    const REPORT_SUPERVISOR_DIR_ROLE = 'REPORT_SUPERVISOR_DIR_ROLE'.__CLASS__;
    const REPORT_LB_QUANTITY_ROLE = 'REPORT_LB_QUANTITY_ROLE'.__CLASS__;
    const REPORT_LB_QUALITY_ROLE = 'REPORT_LB_QUALITY_ROLE'.__CLASS__;
    const REPORT_LB_COMPLETED_EVENTS_ROLE = 'REPORT_LB_COMPLETED_EVENTS_ROLE'.__CLASS__;
    const REPORT_QA_MISTAKES = 'REPORT_QA_MISTAKES'.__CLASS__;
    const REPORT_QA_PRODUCTIVITY = 'REPORT_QA_PRODUCTIVITY'.__CLASS__;
    const REPORT_SHOW_PASSWORD = 'REPORT_SHOW_PASSWORD'.__CLASS__;
    const REPORT_LB_MISTAKES_ROLE = 'REPORT_LB_MISTAKES_ROLE'.__CLASS__;
    const REPORT_CANCEL_ACCOUNTS_ROLE = 'REPORT_CANCEL_ACCOUNTS_ROLE'.__CLASS__;
    const REPORT_NUMBER_EVENTS_ROLE = 'REPORT_NUMBER_EVENTS_ROLE'.__CLASS__;

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [
            self::REPORT_LB_ROLE,
            self::REPORT_TENURED_LB_ROLE,
            self::REPORT_DELAYED_ROLE,
            self::REPORT_QA_WEBSITE_ROLE,
            self::REPORT_DAILY_OUTPUT_ROLE,
            self::REPORT_TECH_SCORE_ROLE,
            self::REPORT_QNQ_ROLE,
            self::REPORT_TECH_QUANTITY_ROLE,
            self::REPORT_REPLACEMENT_DIR_ROLE,
            self::REPORT_SUPERVISOR_DIR_ROLE,
            self::REPORT_LB_QUANTITY_ROLE,
            self::REPORT_LB_QUALITY_ROLE,
            self::REPORT_LB_COMPLETED_EVENTS_ROLE,
            self::REPORT_QA_MISTAKES,
            self::REPORT_QA_PRODUCTIVITY,
            self::REPORT_SHOW_PASSWORD,
            self::REPORT_LB_MISTAKES_ROLE,
            self::REPORT_CANCEL_ACCOUNTS_ROLE,
            self::REPORT_NUMBER_EVENTS_ROLE,
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
            case self::REPORT_CANCEL_ACCOUNTS_ROLE:
                return false;
            case self::REPORT_NUMBER_EVENTS_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

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
    public function getAdvertisingAccess(User $user, $attribute)
    {
        switch ($attribute) {
            case self::REPORT_LB_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_TENURED_LB_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_DELAYED_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_QA_WEBSITE_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_DAILY_OUTPUT_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_QNQ_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_TECH_SCORE_ROLE:
                return true;
            case self::REPORT_LB_QUANTITY_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_LB_QUALITY_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_TECH_QUANTITY_ROLE:
                return true;
            case self::REPORT_REPLACEMENT_DIR_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_LB_COMPLETED_EVENTS_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_QA_MISTAKES:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_SHOW_PASSWORD:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_LB_MISTAKES_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;

            case self::REPORT_CANCEL_ACCOUNTS_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;

            case self::REPORT_NUMBER_EVENTS_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
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
            case self::REPORT_LB_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_TENURED_LB_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_DELAYED_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_QA_WEBSITE_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_DAILY_OUTPUT_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_QNQ_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_TECH_SCORE_ROLE:
                return true;
            case self::REPORT_LB_QUANTITY_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_LB_QUALITY_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_TECH_QUANTITY_ROLE:
                return true;
            case self::REPORT_REPLACEMENT_DIR_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_LB_COMPLETED_EVENTS_ROLE:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_QA_MISTAKES:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;
            case self::REPORT_SHOW_PASSWORD:
                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
                ) {
                    return true;
                }

                return false;

            case self::REPORT_LB_MISTAKES_ROLE:
//                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
//                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
//                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
//                ) {
//                    return true;
//                }

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
            case self::REPORT_LB_ROLE:
                return true;
            case self::REPORT_LB_QUALITY_ROLE:
                return true;
            case self::REPORT_LB_QUANTITY_ROLE:
                return true;
            case self::REPORT_TENURED_LB_ROLE:
                return true;
            case self::REPORT_DELAYED_ROLE:
                return true;
            case self::REPORT_QA_WEBSITE_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
            case self::REPORT_DAILY_OUTPUT_ROLE:
                return !in_array(User::ROLE_USER, $user->getRoles(), true);
            case self::REPORT_QNQ_ROLE:
                return true;
            case self::REPORT_TECH_SCORE_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
            case self::REPORT_TECH_QUANTITY_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
            case self::REPORT_REPLACEMENT_DIR_ROLE:
                return in_array(User::ROLE_TL, $user->getRoles(), true);
            case self::REPORT_LB_COMPLETED_EVENTS_ROLE:
                return in_array(User::ROLE_TL, $user->getRoles(), true);

            case self::REPORT_LB_MISTAKES_ROLE:
//                if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) ||
//                    in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) ||
//                    in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
//                ) {
//                    return true;
//                }

                return true;
            case self::REPORT_CANCEL_ACCOUNTS_ROLE:
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
            case self::REPORT_LB_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
            case self::REPORT_TENURED_LB_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
            case self::REPORT_DELAYED_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
            case self::REPORT_QA_WEBSITE_ROLE:
                return true;
            case self::REPORT_REPLACEMENT_DIR_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
            case self::REPORT_LB_COMPLETED_EVENTS_ROLE:
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
            case self::REPORT_DELAYED_ROLE:
                return true;
            case self::REPORT_QA_WEBSITE_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
            case self::REPORT_QNQ_ROLE:
                return true;
            case self::REPORT_TECH_SCORE_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
            case self::REPORT_TECH_QUANTITY_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
            case self::REPORT_REPLACEMENT_DIR_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
            case self::REPORT_SUPERVISOR_DIR_ROLE:
                return in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true);
            case self::REPORT_LB_COMPLETED_EVENTS_ROLE:
                return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
            case self::REPORT_CANCEL_ACCOUNTS_ROLE:
                return true;
            default:
                return false;
        }
    }
}
