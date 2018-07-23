<?php

/*
 * (c) 411 Marketing
 */

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController.
 */
class SecurityController extends Controller
{

    /**
     * @param Request             $request
     * @param AuthenticationUtils $authUtils
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        $user = $this->getUser();

        if ($user instanceof UserInterface) {
            return $this->redirectToRoute('app_homepage');
        }

        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        return $this->render(
            'security\login.html.twig',
            [
                'form' => $form->createView(),
                'error' => $authUtils->getLastAuthenticationError(),
                'last_username' => $authUtils->getLastUsername(),
            ]
        );
    }

    /**
     * Login check action.
     *
     * @throws \Exception
     */
    public function loginCheck()
    {
        throw new \Exception('How did you end up here?');
    }

    /**
     * Logout action.
     *
     * @throws \Exception
     */
    public function logout()
    {
        throw new \Exception('How did you end up here?');
    }
}
