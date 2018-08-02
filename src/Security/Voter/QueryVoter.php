<?php

/*
 * (c) 411 Marketing
 */

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchy;

class QueryVoter extends Voter
{
    const MENU_ROLE = 'QUERY_MENU_ROLE'.__CLASS__;

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
            self::MENU_ROLE,
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
            case self::MENU_ROLE:
                return $this->getMenuPermission($user, $attribute);
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
    private function getMenuPermission(User $user, $attribute)
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


}
