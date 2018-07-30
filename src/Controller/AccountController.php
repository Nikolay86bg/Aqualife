<?php

namespace App\Controller;

use App\Form\AccountFilterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Doctrine\ORM\Tools\Pagination\Paginator;

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

        $accounts = $entityManager->getRepository('App:Account')->getListQuery($filter, $sort, $order);

        $accounts = (new Paginator($accounts))
            ->setEntityManager($this->getDoctrine()->getManager())
            ->paginate($request->query->get('page'));

        return $this->render('account/index.html.twig', [
//            'filter' => $filter->createView(),
            'accounts' => $accounts,
        ]);
    }
}
