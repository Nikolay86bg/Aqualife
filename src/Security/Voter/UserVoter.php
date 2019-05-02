<?php


namespace App\Security\Voter;

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
    const USER_MENU_ROLE = 'USER_MENU_ROLE'.__CLASS__;

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
            self::USER_MENU_ROLE,
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

        switch ($attribute) {
            case self::USER_MENU_ROLE:
                return $this->getUserMenuPermission($user, $attribute);
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
    private function getUserMenuPermission(User $user, $attribute)
    {
        return in_array(User::ROLE_ADMIN, $user->getRoles(), true);
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


}
