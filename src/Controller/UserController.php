<?php

namespace App\Controller;

use App\Doctrine\ORM\Tools\Pagination\Paginator;
use App\Entity\Position;
use App\Entity\User;
use App\Form\UserFilterType;
use App\Form\UserPasswordType;
use App\Form\UserProfileType;
use App\Form\UserType;
use App\Security\Voter\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class UserController.
 */
class UserController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_LIST_ROLE);

        $filter = $this->createForm(UserFilterType::class);
        $filter->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        $sort = null;
        $order = null;

        if (!empty($request->get('sort')) && !empty($request->get('order'))) {
            $sort = $request->get('sort');
            $order = $request->get('order');
        }

        $users = $entityManager->getRepository('App:User')->getListQuery($filter, $sort, $order);

        $users = (new Paginator($users))
            ->setEntityManager($this->getDoctrine()->getManager())
            ->paginate($request->query->get('page'));

        return $this->render('user/index.html.twig', [
            'filter' => $filter->createView(),
            'users' => $users,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_ADD_ROLE);

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($form->get('position')->getData()) {
                $user->setRoles([$form->get('position')->getData()]);
            }
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(User $user)
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_VIEW_ROLE, $user);

        $deleteForm = $this->createDeleteForm($user);

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function profile(Request $request)
    {
        $user = $this->getUser();
        $editForm = $this->createForm(UserProfileType::class, $user);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->flush();

            $this->addFlash('success', $this->get('translator')->trans('general.flashes.saved'));
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => null,
        ]);
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_EDIT_ROLE, $user);

        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm(UserType::class, $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($editForm->get('position')->getData()) {
                $user->setRoles([$editForm->get('position')->getData()]);
                $em->persist($user);
            }

            $em->flush();

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_EDIT_ROLE, $user);

        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', ['id' => $user->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changePassword(Request $request)
    {
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);

        if (($form->isSubmitted()) && ($form->isValid())) {
            $user = $this->getUser();
            $user->setPlainPassword($form->get('plainPassword')->getData());

            $this->getDoctrine()->getManager()->flush($user);

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render(
            'user/change_password.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param SessionInterface $session
     * @param EntityManagerInterface $entityManager
     * @param string $locale
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeLanguage(SessionInterface $session, EntityManagerInterface $entityManager, string $locale)
    {

        /** @var User $user */
        $user = $this->getUser();
        $user->setLocale($locale);
        $entityManager->persist($user);
        $entityManager->flush();

        $session->set('_locale', $locale);

        return $this->redirectToRoute('app_homepage');
    }

}
