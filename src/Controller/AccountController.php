<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountFilterType;
use App\Form\AccountType;
use App\Service\ColorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Intl\Intl;

/**
 * Class AccountController
 * @package App\Controller
 */
class AccountController extends Controller
{
    /**
     * @Route("/account", name="account")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_LIST_ROLE);

        $filter = $this->createForm(AccountFilterType::class);
        $filter->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        $sort = null;
        $order = null;

        if (!empty($request->get('sort')) && !empty($request->get('order'))) {
            $sort = $request->get('sort');
            $order = $request->get('order');
        }

        $accounts = $entityManager->getRepository(Account::class)->getListQuery($filter, $sort, $order);

        $accounts = (new Paginator($accounts))
            ->setEntityManager($this->getDoctrine()->getManager())
            ->paginate($request->query->get('page'));
        $countries = Intl::getRegionBundle()->getCountryNames();

        return $this->render('account/index.html.twig', [
            'filter' => $filter->createView(),
            'accounts' => $accounts,
            'countries' => $countries,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response.
     */
    public function new(Request $request)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_ADD_ROLE);

        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($account);
            $em->flush();

            return $this->redirectToRoute('account_show', ['id' => $account->getId()]);
        }

        return $this->render('account/new.html.twig', [
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Account $account
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Account $account)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_VIEW_ROLE, $user);

        $countries = Intl::getRegionBundle()->getCountryNames();

        return $this->render('account/show.html.twig', [
            'account' => $account,
            'countries' => $countries,
        ]);
    }

    public function edit(Request $request, Account $account)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_EDIT_ROLE, $user);

        $editForm = $this->createForm(AccountType::class, $account);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->flush();

            return $this->redirectToRoute('account_show', ['id' => $account->getId()]);
        }

        return $this->render('account/edit.html.twig', [
            'account' => $account,
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function teamsSchedule()
    {
        return $this->render('account/current-accounts-list.html.twig', [
            'accounts' => $this->getDoctrine()->getManager()->getRepository(Account::class)->getCurrentAccounts(),
            'color' =>  $this->get(ColorService::class)
        ]);
    }

    /**
     * @param Account $account
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function schedule(Account $account)
    {
        return $this->render('account/schedule.html.twig', [
            'account' => $account,
            'color' =>  $this->get(ColorService::class)
        ]);
    }

    /**
     * @param Account $account
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Account $account)
    {
        $account->setDeletedAt((new \DateTime()));
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', $this->get('translator')->trans('general.flashes.deleted'));

        return $this->redirectToRoute('account_index');
    }
}
