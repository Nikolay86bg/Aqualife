<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Facility;
use App\Entity\MealSchedule;
use App\Entity\Query;
use App\Entity\Schedule;
use App\Form\AccountType;
use App\Form\MealScheduleFilterType;
use App\Form\QueryFilterType;
use App\Form\ScheduleFilterType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Intl\Intl;

/**
 * Class QueryController
 * @package App\Controller
 */
class MealScheduleController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_LIST_ROLE);

        $entityManager = $this->getDoctrine()->getManager();
        $filter = $this->createForm(MealScheduleFilterType::class);
        $filter->handleRequest($request);

        if ($filter->isSubmitted() && $filter->isValid()) {
            $restaurant = $filter->get('restaurant')->getData();
            $from = $filter->get('from')->getData();
            $to = $filter->get('to')->getData();
        }else{
            $restaurant = Query::RESTAURANT1;
            $from = $to = new \DateTime();
        }

        $schedule = $entityManager->getRepository(MealSchedule::class)->getSchedule($restaurant, $from, $to);
        $schedule = $entityManager->getRepository(MealSchedule::class)->prepareSchedule($schedule);
        dump($schedule);

        return $this->render('meal-schedule/index.html.twig', [
            'filter' => $filter->createView(),
            'restaurantId' => $restaurant,
            'schedule' => $schedule
        ]);
    }


}
