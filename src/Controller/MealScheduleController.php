<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Facility;
use App\Entity\MealSchedule;
use App\Entity\Query;
use App\Entity\Schedule;
use App\Form\AccountType;
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

        $filter = $this->createForm(ScheduleFilterType::class);
        $filter->handleRequest($request);

        if ($filter->isSubmitted() && $filter->isValid()) {
            $restaurant = $filter->get('restaurant')->getData();
        }else{
            $restaurant = Query::RESTAURANT1;
        }

        return $this->render('meal-schedule/index.html.twig', [
            'filter' => $filter->createView(),
            'restaurantId' => $restaurant
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function feed(Request $request)
    {

        //todo 6te trqbva da vidq emaila i nai veroqtno nqma da polzvam fullcalendar ami vsi4ko da e custom
        $entityManager = $this->getDoctrine()->getManager();
        $restaurantId = $request->request->get('restaurant_id');

        $start = \DateTime::createFromFormat ( 'Y-m-d\TH:i:s' ,  $request->request->get('start'));
        $end = \DateTime::createFromFormat ( 'Y-m-d\TH:i:s' ,  $request->request->get('end'));

        $schedule = $entityManager->getRepository(MealSchedule::class)->getSchedule($restaurantId, $start, $end);
        $schedule = $entityManager->getRepository(MealSchedule::class)->prepareSchedule($schedule);

        return new JsonResponse($schedule);
    }

}
