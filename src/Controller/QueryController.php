<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Query;
use App\Form\AccountFilterType;
use App\Form\AccountType;
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
class QueryController extends Controller
{
    /**
     * @Route("/query", name="query")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_LIST_ROLE);

        $filter = $this->createForm(QueryFilterType::class);
        $filter->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        $sort = null;
        $order = null;

        if (!empty($request->get('sort')) && !empty($request->get('order'))) {
            $sort = $request->get('sort');
            $order = $request->get('order');
        }

        $queries = $entityManager->getRepository('App:Query')->getListQuery($filter, $sort, $order);

        $queries = (new Paginator($queries))
            ->setEntityManager($this->getDoctrine()->getManager())
            ->paginate($request->query->get('page'));
        $countries = Intl::getRegionBundle()->getCountryNames();

        return $this->render('query/index.html.twig', [
//            'filter' => $filter->createView(),
            'queries' => $queries,
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
        $em = $this->getDoctrine()->getManager();

        $query = new Query();
        $form = $this->createForm(QueryType::class, $query);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            dump($request->request->all());
            exit;

            $dates = explode(' - ',$request->get('datetimes'));

            $query->setCreatedBy($this->getUser());
            $query->setDateOfArrival((\DateTime::createFromFormat("d/m/Y H:i",$dates[0])));
            $query->setDateOfDeparture((\DateTime::createFromFormat("d/m/Y H:i",$dates[1])));

            $em->persist($query);
            $em->flush();

            return $this->redirectToRoute('query_show', ['id' => $query->getId()]);
        }

        $facilities = $em->getRepository('App:Facility')->findAll();

        return $this->render('query/new.html.twig', [
            'query' => $query,
            'form' => $form->createView(),
            'facilities' => $facilities,
        ]);
    }

    /**
     * @param Query $query
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Query $query)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_VIEW_ROLE, $user);

        $countries = Intl::getRegionBundle()->getCountryNames();

        return $this->render('query/show.html.twig', [
            'query' => $query,
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
}
