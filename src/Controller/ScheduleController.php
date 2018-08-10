<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Facility;
use App\Entity\Query;
use App\Entity\Schedule;
use App\Form\AccountType;
use App\Form\QueryFilterType;
use App\Form\ScheduleFilterType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Intl\Intl;

/**
 * Class QueryController
 * @package App\Controller
 */
class ScheduleController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_LIST_ROLE);

        $filter = $this->createForm(ScheduleFilterType::class);
        $filter->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        $sort = null;
        $order = null;

        $schedule = $entityManager->getRepository('App:Schedule')->getListQuery($filter, $sort, $order)->getResult();

        $schedule = $entityManager->getRepository('App:Schedule')->orderByDate($schedule);

        dump($schedule);

//        $period = $entityManager->getRepository('App:Schedule')->getDatesBetween(
//            $filter->get('from')->getData(),
//            $filter->get('to')->getData()
//        );

        $facility = $entityManager->getRepository('App:Facility')->findOneBy(['id' => $request->get('facility')]);

        return $this->render('schedule/index.html.twig', [
            'filter' => $filter->createView(),
            'schedules' => $schedule,
//            'period' => $period,
            'facility' => $facility
        ]);
    }

}
