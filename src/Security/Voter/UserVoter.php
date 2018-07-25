<?php

/*
 * (c) 411 Marketing
 */

namespace App\Security\Voter;

//use App\Entity\Department;
use App\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchy;

class UserVoter extends Voter
{
    const USER_VIEW_ROLE = 'USER_VIEW_ROLE'.__CLASS__;
    const USER_EDIT_ROLE = 'USER_EDIT_ROLE'.__CLASS__;
    const USER_ADD_ROLE = 'USER_ADD_ROLE'.__CLASS__;
    const USER_LIST_ROLE = 'USER_LIST_ROLE'.__CLASS__;
    const USER_DELETE_ROLE = 'USER_DELETE_ROLE'.__CLASS__;

    /**
     * @var RoleHierarchyVoter
     */
    private $roleHierarchyVoter;

    /**
     * ManageUserVoter constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->roleHierarchyVoter = new RoleHierarchyVoter(new RoleHierarchy($container->getParameter('security.role_hierarchy.roles')));
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [
            self::USER_VIEW_ROLE,
            self::USER_EDIT_ROLE,
            self::USER_ADD_ROLE,
            self::USER_LIST_ROLE,
            self::USER_DELETE_ROLE,
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

        if (self::USER_LIST_ROLE !== $attribute && self::USER_ADD_ROLE !== $attribute) {
            $token = new AnonymousToken(rand(), $subject, $subject->getRoles());

            //1 = current user don't have permissions || -1 = current user have permissions
            //AND if current user department is in MANAGERS_DEPARTMENT_PERMISSIONS
            if (
                1 === $this->roleHierarchyVoter->vote($token, $user, $user->getRoles())
//                ||
//                !in_array($subject->getDepartment()->getName(), USER::MANAGERS_DEPARTMENT_PERMISSIONS[$user->getDepartment()->getName()], true)
            ) {
                return false;
            }
        }

        return true;

//        switch ($user->getDepartment()->getId()) {
//            case Department::QUALITY_ASSURANCE_DEPARTMENT:
//                return $this->getQAAccess($user, $attribute);
//            case Department::ADVERTISING_DEPARTMENT:
//                return $this->getAdvertisingAccess($user, $attribute);
//            case Department::TECHNICAL_SUPPORT_DEPARTMENT:
//                return $this->getTechnicalSupportAccess($user, $attribute);
//            case Department::LINK_BUILDING_DEPARTMENT:
//                return $this->getLBAccess($user, $attribute);
//            case Department::WEB_DESIGN_DEPARTMENT:
//                return $this->getWebDesignAccess($user, $attribute);
//            case Department::CONTENT_WRITING_DEPARTMENT:
//                return $this->getContentWritingAccess($user, $attribute);
//            case Department::TRAINING_DEPARTMENT:
//                return $this->getTrainingAccess($user, $attribute);
//            case Department::REPORTING_DEPARTMENT:
//                return $this->getQAAccess($user, $attribute);
//            default:
//                return false;
//        }
    }

    /**
     * @param User $user
     * @param $attribute
     *
     * @return bool
     */
    private function getQAAccess(User $user, $attribute)
    {
        return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
    }

    /**
     * @param User $user
     * @param $attribute
     *
     * @return bool
     */
    private function getAdvertisingAccess(User $user, $attribute)
    {
        if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) or
            in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) or
            in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $attribute
     *
     * @return bool
     */
    private function getTechnicalSupportAccess(User $user, $attribute)
    {
        if (in_array(User::ROLE_MANAGER, $user->getRoles(), true) or
            in_array(User::ROLE_SUPERVISOR, $user->getRoles(), true) or
            in_array(User::ROLE_OPERATIONS_MANAGER, $user->getRoles(), true)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $attribute
     *
     * @return bool
     */
    private function getLBAccess(User $user, $attribute)
    {
        return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
    }

    /**
     * @param User $user
     * @param $attribute
     *
     * @return bool
     */
    public function getWebDesignAccess(User $user, $attribute)
    {
        return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
    }

    /**
     * @param User $user
     * @param $attribute
     *
     * @return bool
     */
    private function getContentWritingAccess(User $user, $attribute)
    {
        return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
    }

    /**
     * @param User $user
     * @param User $subject
     *
     * @return bool
     */
    private function getTrainingAccess(User $user, $subject)
    {
        return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
    }

    /**
     * @param User $user
     * @param $attribute
     *
     * @return bool
     */
    private function getCustomWebAccess(User $user, $attribute)
    {
        return in_array(User::ROLE_MANAGER, $user->getRoles(), true);
    }
}
