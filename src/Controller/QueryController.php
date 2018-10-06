<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Facility;
use App\Entity\MealSchedule;
use App\Entity\Query;
use App\Entity\Schedule;
use App\Form\AccountType;
use App\Form\QueryFilterType;
use App\Security\Voter\QueryVoter;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Intl\Intl;
use App\Service\MailerService;

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
        $sort = 'id';
        $order = 'DESC';

        if (!empty($request->get('sort')) && !empty($request->get('order'))) {
            $sort = $request->get('sort');
            $order = $request->get('order');
        }

        $scheduleRepo = $entityManager->getRepository('App:Schedule');

        $queries = $entityManager->getRepository(Query::class)->getListQuery($filter, $sort, $order);

        $queries = (new Paginator($queries))
            ->setEntityManager($this->getDoctrine()->getManager())
            ->paginate($request->query->get('page'));
        $countries = Intl::getRegionBundle()->getCountryNames();

        return $this->render('query/index.html.twig', [
            'filter' => $filter->createView(),
            'queries' => $queries,
            'countries' => $countries,
            'scheduleRepo' => $scheduleRepo
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function new(Request $request)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_ADD_ROLE);
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($account);

            foreach ($request->get('facilities') as $facilityId => $facility) {
                $facilityReference = $em->getReference(Facility::class, $facilityId);

                foreach ($facility['date'] as $key => $value) {
                    if (!empty($value)) {
                        if (isset($facility['mTimeFrom'][$key])) {
                            if ($facility['mTimeFrom'][$key] != '' && $facility['mTimeTo'][$key] != '') {
                                $schedule = new Schedule();
                                $schedule->setDate((\DateTime::createFromFormat("d/m/Y", $value)));
                                $schedule->setFacility($facilityReference);
                                $schedule->setParts($facility['part'][$key]);
                                $schedule->setTimeFrom(\DateTime::createFromFormat('H:i', $facility['mTimeFrom'][$key]));
                                $schedule->setTimeTo(\DateTime::createFromFormat('H:i', $facility['mTimeTo'][$key]));
                                $schedule->setAccount($account);

                                $em->persist($schedule);
                            }
                        }

                        if (isset($facility['aTimeFrom'][$key])) {
                            if ($facility['aTimeFrom'][$key] != '' && $facility['aTimeTo'][$key] != '') {
                                $schedule = new Schedule();
                                $schedule->setDate((\DateTime::createFromFormat("d/m/Y", $value)));
                                $schedule->setFacility($facilityReference);
                                $schedule->setParts($facility['part'][$key]);
                                $schedule->setTimeFrom(\DateTime::createFromFormat('H:i', $facility['aTimeFrom'][$key]));
                                $schedule->setTimeTo(\DateTime::createFromFormat('H:i', $facility['aTimeTo'][$key]));
                                $schedule->setAccount($account);

                                $em->persist($schedule);
                            }
                        }

                    }
                }
            }

            foreach ($request->get('meals')['date'] as $key => $value) {
                if (!empty($value)) {
                    $mealSchedule = new MealSchedule();
                    $mealSchedule->setDate((\DateTime::createFromFormat("d/m/Y", $value)));
                    $mealSchedule->setAccount($account);

                    if ($request->get('meals')['restaurant'][$key] != "") {
                        $mealSchedule->setRestaurant($request->get('meals')['restaurant'][$key]);
                    }
                    if ($request->get('meals')['breakfast'][$key] != "") {
                        $mealSchedule->setBreakfastTime(\DateTime::createFromFormat('H:i', $request->get('meals')['breakfast'][$key]));
                    }
                    if ($request->get('meals')['middle_breakfast'][$key] != "") {
                        $mealSchedule->setMiddleBreakfastTime(\DateTime::createFromFormat('H:i', $request->get('meals')['middle_breakfast'][$key]));
                    }
                    if ($request->get('meals')['lunch'][$key] != "") {
                        $mealSchedule->setLunchTime(\DateTime::createFromFormat('H:i', $request->get('meals')['lunch'][$key]));
                    }
                    if ($request->get('meals')['dinner'][$key] != "") {
                        $mealSchedule->setDinnerTime(\DateTime::createFromFormat('H:i', $request->get('meals')['dinner'][$key]));
                    }

                    $em->persist($mealSchedule);
                }
            }

            $dates = explode(' - ', $request->get('datetimes'));

            $query = new Query();

            $query->setAccount($account);
            $query->setHotel($request->get('hotel'));
            $query->setCreatedBy($this->getUser());
            $query->setDateOfArrival((\DateTime::createFromFormat("d/m/Y H:i", $dates[0])));
            $query->setDateOfDeparture((\DateTime::createFromFormat("d/m/Y H:i", $dates[1])));

            $em->persist($query);

            $em->flush();

            $this->addFlash('success', 'Query was created!');

            $this->get(MailerService::class)->sendMail($account);

            return $this->redirectToRoute('query_show', ['id' => $query->getId()]);
        }

        $facilities = $em->getRepository('App:Facility')->findAll();

        return $this->render('query/new.html.twig', [
            'account' => $account,
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

        $scheduleArray = $mealArray = [];
//        if($schedules = $query->getAccount()->getSchedules()){
        if ($schedules = $this->getDoctrine()->getManager()->getRepository(Schedule::class)->findBy([
            'account' => $query->getAccount(),
            'deleted' => null,
        ], [
            'date' => 'ASC',
            'timeFrom' => 'ASC',
        ])
        ) {
            foreach ($schedules as $schedule) {
                $scheduleArray[$schedule->getDate()->format('Y-m-d')][] = $schedule;
            }
        }

        if ($meals = $this->getDoctrine()->getManager()->getRepository(MealSchedule::class)->findBy([
            'account' => $query->getAccount(),
            'deleted' => null,
        ], [
            'date' => 'ASC'
        ])
        ) {
            foreach ($meals as $meal) {
                $mealArray[$meal->getDate()->format('Y-m-d')][] = $meal;
            }
        }

        ksort($scheduleArray);
        ksort($mealArray);

        return $this->render('query/show.html.twig', [
            'query' => $query,
            'countries' => $countries,
            'scheduleArray' => $scheduleArray,
            'mealArray' => $mealArray,
        ]);
    }

    /**
     * @param Request $request
     * @param Query $query
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function edit(Request $request, Query $query)
    {
        $this->denyAccessUnlessGranted(QueryVoter::QUERY_EDIT_ROLE);

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = $query->getAccount();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dates = explode(' - ', $request->get('datetimes'));

            $query->setHotel($request->get('hotel'));
            $query->setDateOfArrival((\DateTime::createFromFormat("d/m/Y H:i", $dates[0])));
            $query->setDateOfDeparture((\DateTime::createFromFormat("d/m/Y H:i", $dates[1])));

            $em->persist($account);
            $em->persist($query);

            //ADD NEW DATES
            if ($request->get('newschedules')) {
                foreach ($request->get('newschedules')['date'] as $facilityId => $value) {
                    $facility = $em->getReference(Facility::class, $facilityId);

                    foreach ($value as $key => $date) {
                        if ($date) {
                            $schedule = new Schedule();
                            $schedule->setDate((\DateTime::createFromFormat("d/m/Y", $date)));
                            $schedule->setAccount($account);
                            $schedule->setFacility($facility);

                            $schedule->setParts($request->get('newschedules')['part'][$facilityId][$key]);
                            $schedule->setTimeFrom(\DateTime::createFromFormat('H:i', $request->get('newschedules')['mTimeFrom'][$facilityId][$key]));
                            $schedule->setTimeTo(\DateTime::createFromFormat('H:i', $request->get('newschedules')['mTimeTo'][$facilityId][$key]));

                            $em->persist($schedule);
                        }
                    }
                }
            }

            if ($request->get('facilities')) {
                foreach ($account->getSchedules() as $oldSchedule) {
                    if (array_key_exists($oldSchedule->getId(), $request->get('facilities')['date'])) {
                        $oldSchedule->setDate((\DateTime::createFromFormat("d/m/Y", $request->get('facilities')['date'][$oldSchedule->getId()])));
                        $oldSchedule->setParts($request->get('facilities')['part'][$oldSchedule->getId()]);
                        $oldSchedule->setTimeFrom(\DateTime::createFromFormat('H:i', $request->get('facilities')['mTimeFrom'][$oldSchedule->getId()]));
                        $oldSchedule->setTimeTo(\DateTime::createFromFormat('H:i', $request->get('facilities')['mTimeTo'][$oldSchedule->getId()]));
                        $oldSchedule->setAccount($account);
                    } else {
                        //DELETE THE OLD SCHEDULE
                        $oldSchedule->setDeleted((new \DateTime()));
                    }
                    $em->persist($oldSchedule);
                }
            } else {
//                If all are deleted
                foreach ($account->getSchedules() as $oldSchedule) {
                    $oldSchedule->setDeleted((new \DateTime()));
                    $em->persist($oldSchedule);
                }
            }

            //ADD NEW DATES
            if ($request->get('newmeals')) {
                foreach ($request->get('newmeals')['date'] as $key => $date) {
                    if ($date) {
                        $mealSchedule = new MealSchedule();
                        $mealSchedule->setDate((\DateTime::createFromFormat("d/m/Y", $date)));
                        $mealSchedule->setAccount($account);

                        if ($request->get('newmeals')['restaurant'][$key] != "") {
                            $mealSchedule->setRestaurant($request->get('newmeals')['restaurant'][$key]);
                        }
                        if ($request->get('newmeals')['breakfast'][$key] != "" && $request->get('newmeals')['breakfast'][$key] != "0:00") {
                            $mealSchedule->setBreakfastTime(\DateTime::createFromFormat('H:i', $request->get('newmeals')['breakfast'][$key]));
                        }
                        if ($request->get('newmeals')['middle_breakfast'][$key] != "" && $request->get('newmeals')['middle_breakfast'][$key] != "0:00") {
                            $mealSchedule->setMiddleBreakfastTime(\DateTime::createFromFormat('H:i', $request->get('newmeals')['middle_breakfast'][$key]));
                        }
                        if ($request->get('newmeals')['lunch'][$key] != "" && $request->get('newmeals')['lunch'][$key] != "0:00") {
                            $mealSchedule->setLunchTime(\DateTime::createFromFormat('H:i', $request->get('newmeals')['lunch'][$key]));
                        }
                        if ($request->get('newmeals')['dinner'][$key] != "" && $request->get('newmeals')['dinner'][$key] != "0:00") {
                            $mealSchedule->setDinnerTime(\DateTime::createFromFormat('H:i', $request->get('newmeals')['dinner'][$key]));
                        }
                        $em->persist($mealSchedule);
                    }
                }
            }

            if ($request->get('meals')['date']) {
                foreach ($account->getMealSchedules() as $oldMeal) {
                    //Edit
                    if (array_key_exists($oldMeal->getId(), $request->get('meals')['date'])) {
                        $oldMeal->setDate((\DateTime::createFromFormat("d/m/Y", $request->get('meals')['date'][$oldMeal->getId()])));
                        $oldMeal->setAccount($account);

                        if ($request->get('meals')['restaurant'][$oldMeal->getId()] != "") {
                            $oldMeal->setRestaurant($request->get('meals')['restaurant'][$oldMeal->getId()]);
                        }
                        if ($request->get('meals')['breakfast'][$oldMeal->getId()] != "" && $request->get('meals')['breakfast'][$oldMeal->getId()] != "0:00") {
                            $oldMeal->setBreakfastTime(\DateTime::createFromFormat('H:i', $request->get('meals')['breakfast'][$oldMeal->getId()]));
                        } else {
                            $oldMeal->setBreakfastTime(null);
                        }

                        if ($request->get('meals')['middle_breakfast'][$oldMeal->getId()] != "" && $request->get('meals')['middle_breakfast'][$oldMeal->getId()] != "0:00") {
                            $oldMeal->setMiddleBreakfastTime(\DateTime::createFromFormat('H:i', $request->get('meals')['middle_breakfast'][$oldMeal->getId()]));
                        } else {
                            $oldMeal->setMiddleBreakfastTime(null);
                        }

                        if ($request->get('meals')['lunch'][$oldMeal->getId()] != "" && $request->get('meals')['lunch'][$oldMeal->getId()] != "0:00") {
                            $oldMeal->setLunchTime(\DateTime::createFromFormat('H:i', $request->get('meals')['lunch'][$oldMeal->getId()]));
                        } else {
                            $oldMeal->setLunchTime(null);
                        }

                        if ($request->get('meals')['dinner'][$oldMeal->getId()] != "" && $request->get('meals')['dinner'][$oldMeal->getId()] != "0:00") {
                            $oldMeal->setDinnerTime(\DateTime::createFromFormat('H:i', $request->get('meals')['dinner'][$oldMeal->getId()]));
                        } else {
                            $oldMeal->setDinnerTime(null);
                        }

                    } else {
                        //DELETE THE OLD MEAL
                        $oldMeal->setDeleted((new \DateTime()));
                    }

                    $em->persist($oldMeal);
                }
            } else {
                //If all are deleted
                foreach ($account->getMealSchedules() as $oldMeal) {
                    $oldMeal->setDeleted((new \DateTime()));
                    $em->persist($oldMeal);
                }
            }

            $em->flush();
            $this->addFlash('success', 'Query was updated!');

            return $this->redirectToRoute('query_edit', ['id' => $query->getId()]);
        }

        $facilities = $em->getRepository('App:Facility')->findAll();

//        $schedules = $query->getAccount()->getSchedules();
        $schedules = $em->getRepository(Schedule::class)->findBy([
            'account' => $query->getAccount(),
            'deleted' => null,
        ], [
            'date' => 'ASC',
            'timeFrom' => 'ASC',
        ]);

        $usedFacilities = [];
        foreach ($schedules as $schedule) {
            if (!in_array($schedule->getFacility()->getId(), $usedFacilities)) {
                array_push($usedFacilities, $schedule->getFacility()->getId());
            }
        }

        $scheduleRepo = $em->getRepository('App:Schedule');

        return $this->render('query/edit.html.twig', [
            'query' => $query,
            'form' => $form->createView(),
            'facilities' => $facilities,
            'used_facilities' => $usedFacilities,
            'schedules' => $schedules,
//            'meal_schedules' => $query->getAccount()->getMealSchedules(),
            'meal_schedules' => $em->getRepository(MealSchedule::class)->findBy([
                'account' => $query->getAccount(),
                'deleted' => null,
            ], [
                'date' => 'ASC'
            ]),
            'scheduleRepo' => $scheduleRepo,
        ]);
    }

    /**
     * @param Query $query
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function reject(Query $query)
    {
        $this->denyAccessUnlessGranted(QueryVoter::QUERY_EDIT_ROLE);

        $query->setStatus(Query::STATUS_REJECTED);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Query was updated!');

        return $this->redirectToRoute('query_index');
    }

    /**
     * @param Query $query
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function inProgress(Query $query)
    {
        $this->denyAccessUnlessGranted(QueryVoter::QUERY_EDIT_ROLE);

        $query->setStatus(Query::STATUS_IN_PROGRESS);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Query was updated!');

        return $this->redirectToRoute('query_index');
    }

    /**
     * @param Request $request
     * @param Query $query
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function accept(Request $request, Query $query)
    {
        $this->denyAccessUnlessGranted(QueryVoter::QUERY_EDIT_ROLE);

        $em = $this->getDoctrine()->getManager();
        $query->setStatus(Query::STATUS_ACCEPTED);

        if ($request->request->has('lanes')) {
            foreach ($request->request->get('lanes') as $scheduleId => $lanes) {
                $schedule = $em->getRepository('App:Schedule')->findOneBy([
                    'id' => $scheduleId
                ]);
                $schedule->setLanes(serialize($lanes));
            }
        }

        $em->flush();

        $this->addFlash('success', 'Query was updated!');

        return $this->redirectToRoute('query_index');
    }

    /**
     * @param Query $query
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function payed(Query $query)
    {
        $query->setPayed(Query::PAYED_YES);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Query was updated!');

        return $this->redirectToRoute('query_index');
    }

    /**
     * @param Query $query
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Query $query)
    {
        $query->setDeletedAt((new \DateTime()));
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Query was deleted successfully!');

        return $this->redirectToRoute('query_index');
    }
}
