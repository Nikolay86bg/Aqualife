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
use App\Service\ColorService;
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
        } else {
            $restaurant = null;
            $from = $to = new \DateTime();
        }

        $schedule = $entityManager->getRepository(MealSchedule::class)->getSchedule($from, $to, $restaurant);
//        $schedule = $entityManager->getRepository(MealSchedule::class)->getSchedule($from, $to);
        $schedule = $entityManager->getRepository(MealSchedule::class)->prepareSchedule($schedule);

        return $this->render('meal-schedule/index.html.twig', [
            'filter' => $filter->createView(),
//            'restaurantId' => $restaurant,
            'from' => $from->format('d-m-Y'),
            'to' => $to->format('d-m-Y'),
            'schedule' => $schedule,
            'color' => $this->get(ColorService::class),
        ]);
    }

    /**
     * @param int $id
     * @param \DateTime $from
     * @param \DateTime $to
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function printTemplate(int $id, \DateTime $from, \DateTime $to)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $schedule = $entityManager->getRepository(MealSchedule::class)->getSchedule($from, $to, $id);
        $scheduleOrdered = $entityManager->getRepository(MealSchedule::class)->prepareSchedule($schedule);

        return $this->render('meal-schedule/print.html.twig', [
            'schedule' => $scheduleOrdered,
            'restaurantID' => $id,
        ]);
    }


}
