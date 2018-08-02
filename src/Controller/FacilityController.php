<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Facility;
use App\Entity\Query;
use App\Form\AccountFilterType;
use App\Form\AccountType;
use App\Form\FacilityFilterType;
use App\Form\FacilityType;
use App\Form\QueryFilterType;
use App\Form\QueryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Intl\Intl;

/**
 * Class QueryController
 * @package App\Controller
 */
class FacilityController extends Controller
{
    /**
     * @Route("/facility", name="facility")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_LIST_ROLE);

        $filter = $this->createForm(FacilityFilterType::class);
        $filter->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        $sort = null;
        $order = null;

        if (!empty($request->get('sort')) && !empty($request->get('order'))) {
            $sort = $request->get('sort');
            $order = $request->get('order');
        }

        $facilities = $entityManager->getRepository('App:Facility')->getListQuery($filter, $sort, $order);

        $facilities = (new Paginator($facilities))
            ->setEntityManager($this->getDoctrine()->getManager())
            ->paginate($request->query->get('page'));

        return $this->render('facility/index.html.twig', [
//            'filter' => $filter->createView(),
            'facilities' => $facilities,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response.
     */
    public function new(Request $request)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_ADD_ROLE);

        $facility = new Facility();
        $form = $this->createForm(FacilityType::class, $facility);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($facility);
            $em->flush();

            return $this->redirectToRoute('facility_show', ['id' => $facility->getId()]);
        }

        return $this->render('facility/new.html.twig', [
            'facility' => $facility,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Facility $facility
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Facility $facility)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_VIEW_ROLE, $user);
        return $this->render('facility/show.html.twig', [
            'facility' => $facility,
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
}
