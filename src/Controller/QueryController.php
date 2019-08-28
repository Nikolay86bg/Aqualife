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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Intl\Intl;
use App\Service\MailerService;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class QueryController
 * @package App\Controller
 */
class QueryController extends AbstractController
{
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var MailerService
     */
    private $mailerService;

    public function __construct(TranslatorInterface $translator, MailerService $mailerService)
    {
        $this->translator = $translator;
        $this->mailerService = $mailerService;
    }

    /**
     * @Route("/query", name="query")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_LIST_ROLE);

        $filter = $this->createForm(QueryFilterType::class);
        $filter->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        $sort = 'dateOfArrival';
        $order = 'ASC';

        if (!empty($request->get('sort')) && !empty($request->get('order'))) {
            $sort = $request->get('sort');
            $order = $request->get('order');
        }

        $query = $entityManager->getRepository(Query::class)->getListQuery($filter, $sort, $order);

        $queries = (new Paginator($query))
            ->setEntityManager($entityManager)
            ->paginate($request->query->get('page'));

        return $this->render('query/index.html.twig', [
            'filter' => $filter->createView(),
            'queries' => $queries,
            'countries' => Intl::getRegionBundle()->getCountryNames(),
            'scheduleRepo' => $entityManager->getRepository(Schedule::class)
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
                    if ($request->get('meals')['breakfast_end'][$key] != "") {
                        $mealSchedule->setBreakfastTimeEnd(\DateTime::createFromFormat('H:i', $request->get('meals')['breakfast_end'][$key]));
                    }
                    if ($request->get('meals')['middle_breakfast'][$key] != "") {
                        $mealSchedule->setMiddleBreakfastTime(\DateTime::createFromFormat('H:i', $request->get('meals')['middle_breakfast'][$key]));
                    }
                    if ($request->get('meals')['middle_breakfast_end'][$key] != "") {
                        $mealSchedule->setMiddleBreakfastTimeEnd(\DateTime::createFromFormat('H:i', $request->get('meals')['middle_breakfast_end'][$key]));
                    }
                    if ($request->get('meals')['lunch'][$key] != "") {
                        $mealSchedule->setLunchTime(\DateTime::createFromFormat('H:i', $request->get('meals')['lunch'][$key]));
                    }
                    if ($request->get('meals')['lunch_end'][$key] != "") {
                        $mealSchedule->setLunchTimeEnd(\DateTime::createFromFormat('H:i', $request->get('meals')['lunch_end'][$key]));
                    }
                    if ($request->get('meals')['dinner'][$key] != "") {
                        $mealSchedule->setDinnerTime(\DateTime::createFromFormat('H:i', $request->get('meals')['dinner'][$key]));
                    }
                    if ($request->get('meals')['dinner_end'][$key] != "") {
                        $mealSchedule->setDinnerTimeEnd(\DateTime::createFromFormat('H:i', $request->get('meals')['dinner_end'][$key]));
                    }

                    $em->persist($mealSchedule);
                }
            }

            $dates = explode(' - ', $request->get('datetimes'));

            $hotels = implode(',', $request->get('hotel'));
            $query = new Query();
            $query->setAccount($account);
            $query->setHotel($hotels);
            $query->setNumberOfPeople($request->get('number_of_people'));
            $query->setCreatedBy($this->getUser());
            $query->setDateOfArrival((\DateTime::createFromFormat("d/m/Y H:i", $dates[0])));
            $query->setDateOfDeparture((\DateTime::createFromFormat("d/m/Y H:i", $dates[1])));

            $em->persist($query);

            $em->flush();

            $this->addFlash('success', $this->translator->trans('general.flashes.saved'));

            $this->mailerService->sendMail($account);

            return $this->redirectToRoute('query_show', ['id' => $query->getId()]);
        }

        $facilities = $em->getRepository(Facility::class)->findAll();

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
    public function show(Query $query, Request $request)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_VIEW_ROLE, $user);
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
            'countries' => Intl::getRegionBundle()->getCountryNames(),
            'scheduleArray' => $scheduleArray,
            'mealArray' => $mealArray,
            'backUrl' => $request->server->get('HTTP_REFERER'),
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

//        phpinfo();
//        dump($request);
//        exit;

        if ($form->isSubmitted() && $form->isValid()) {
            $dates = explode(' - ', $request->get('datetimes'));
            $hotels = implode(',', $request->get('hotel'));

            $query->setHotel($hotels);
            $query->setNumberOfPeople($request->get('number_of_people'));
            $query->setDateOfArrival((\DateTime::createFromFormat("d/m/Y H:i", $dates[0])));
            $query->setDateOfDeparture((\DateTime::createFromFormat("d/m/Y H:i", $dates[1])));

            $em->persist($account);
            $em->persist($query);

            //Edit existing should be before adding new because here is the delete functionality
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
                            $em->flush();
                        }
                    }
                }
            }

            //Edit existing should be before adding new because here is the delete functionality
            if ($request->get('meals')['date']) {
                foreach ($account->getMealSchedules() as $oldMeal) {
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

                        if ($request->get('meals')['breakfast_end'][$oldMeal->getId()] != "" && $request->get('meals')['breakfast_end'][$oldMeal->getId()] != "0:00") {
                            $oldMeal->setBreakfastTimeEnd(\DateTime::createFromFormat('H:i', $request->get('meals')['breakfast_end'][$oldMeal->getId()]));
                        } else {
                            $oldMeal->setBreakfastTimeEnd(null);
                        }

                        if ($request->get('meals')['middle_breakfast'][$oldMeal->getId()] != "" && $request->get('meals')['middle_breakfast'][$oldMeal->getId()] != "0:00") {
                            $oldMeal->setMiddleBreakfastTime(\DateTime::createFromFormat('H:i', $request->get('meals')['middle_breakfast'][$oldMeal->getId()]));
                        } else {
                            $oldMeal->setMiddleBreakfastTime(null);
                        }

                        if ($request->get('meals')['middle_breakfast_end'][$oldMeal->getId()] != "" && $request->get('meals')['middle_breakfast_end'][$oldMeal->getId()] != "0:00") {
                            $oldMeal->setMiddleBreakfastTimeEnd(\DateTime::createFromFormat('H:i', $request->get('meals')['middle_breakfast_end'][$oldMeal->getId()]));
                        } else {
                            $oldMeal->setMiddleBreakfastTimeEnd(null);
                        }

                        if ($request->get('meals')['lunch'][$oldMeal->getId()] != "" && $request->get('meals')['lunch'][$oldMeal->getId()] != "0:00") {
                            $oldMeal->setLunchTime(\DateTime::createFromFormat('H:i', $request->get('meals')['lunch'][$oldMeal->getId()]));
                        } else {
                            $oldMeal->setLunchTime(null);
                        }

                        if ($request->get('meals')['lunch_end'][$oldMeal->getId()] != "" && $request->get('meals')['lunch_end'][$oldMeal->getId()] != "0:00") {
                            $oldMeal->setLunchTimeEnd(\DateTime::createFromFormat('H:i', $request->get('meals')['lunch_end'][$oldMeal->getId()]));
                        } else {
                            $oldMeal->setLunchTimeEnd(null);
                        }

                        if ($request->get('meals')['dinner'][$oldMeal->getId()] != "" && $request->get('meals')['dinner'][$oldMeal->getId()] != "0:00") {
                            $oldMeal->setDinnerTime(\DateTime::createFromFormat('H:i', $request->get('meals')['dinner'][$oldMeal->getId()]));
                        } else {
                            $oldMeal->setDinnerTime(null);
                        }

                        if ($request->get('meals')['dinner_end'][$oldMeal->getId()] != "" && $request->get('meals')['dinner_end'][$oldMeal->getId()] != "0:00") {
                            $oldMeal->setDinnerTimeEnd(\DateTime::createFromFormat('H:i', $request->get('meals')['dinner_end'][$oldMeal->getId()]));
                        } else {
                            $oldMeal->setDinnerTimeEnd(null);
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
                        if ($request->get('newmeals')['breakfast_end'][$key] != "" && $request->get('newmeals')['breakfast_end'][$key] != "0:00") {
                            $mealSchedule->setBreakfastTimeEnd(\DateTime::createFromFormat('H:i', $request->get('newmeals')['breakfast_end'][$key]));
                        }
                        if ($request->get('newmeals')['middle_breakfast'][$key] != "" && $request->get('newmeals')['middle_breakfast'][$key] != "0:00") {
                            $mealSchedule->setMiddleBreakfastTime(\DateTime::createFromFormat('H:i', $request->get('newmeals')['middle_breakfast'][$key]));
                        }
                        if ($request->get('newmeals')['middle_breakfast_end'][$key] != "" && $request->get('newmeals')['middle_breakfast_end'][$key] != "0:00") {
                            $mealSchedule->setMiddleBreakfastTimeEnd(\DateTime::createFromFormat('H:i', $request->get('newmeals')['middle_breakfast_end'][$key]));
                        }
                        if ($request->get('newmeals')['lunch'][$key] != "" && $request->get('newmeals')['lunch'][$key] != "0:00") {
                            $mealSchedule->setLunchTime(\DateTime::createFromFormat('H:i', $request->get('newmeals')['lunch'][$key]));
                        }
                        if ($request->get('newmeals')['lunch_end'][$key] != "" && $request->get('newmeals')['lunch_end'][$key] != "0:00") {
                            $mealSchedule->setLunchTimeEnd(\DateTime::createFromFormat('H:i', $request->get('newmeals')['lunch_end'][$key]));
                        }
                        if ($request->get('newmeals')['dinner'][$key] != "" && $request->get('newmeals')['dinner'][$key] != "0:00") {
                            $mealSchedule->setDinnerTime(\DateTime::createFromFormat('H:i', $request->get('newmeals')['dinner'][$key]));
                        }
                        if ($request->get('newmeals')['dinner_end'][$key] != "" && $request->get('newmeals')['dinner_end'][$key] != "0:00") {
                            $mealSchedule->setDinnerTimeEnd(\DateTime::createFromFormat('H:i', $request->get('newmeals')['dinner_end'][$key]));
                        }
                        $em->persist($mealSchedule);
                        $em->flush();
                    }
                }
            }

            $em->flush();
            $this->addFlash('success', $this->translator->trans('general.flashes.saved'));

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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function acceptForm(Query $query)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $countries = Intl::getRegionBundle()->getCountryNames();
        $scheduleRepo = $entityManager->getRepository(Schedule::class);

        return $this->render('query/accept-form.html.twig', [
            'query' => $query,
            'countries' => $countries,
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

        $this->addFlash('success', $this->translator->trans('general.flashes.saved'));

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

        $this->addFlash('success', $this->translator->trans('general.flashes.saved'));

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
                $schedule = $em->getRepository(Schedule::class)->findOneBy([
                    'id' => $scheduleId
                ]);
                $schedule->setLanes(serialize($lanes));
            }
        }

        $em->flush();

        $this->addFlash('success', $this->translator->trans('general.flashes.saved'));

        return $this->redirectToRoute('query_index');
    }

    /**
     * @param Query $query
     * @param Request $request
     * @return mixed
     */
    public function payed(Query $query, Request $request)
    {
        $query->setPayed(Query::PAYED_YES);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', $this->translator->trans('general.flashes.saved'));

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Query $query
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Query $query)
    {
        $query->setDeletedAt((new \DateTime()));
        $query->getAccount()->setDeletedAt((new \DateTime()));
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', $this->translator->trans('general.flashes.deleted'));

        return $this->redirectToRoute('query_index');
    }

    /**
     * @param Query $query
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function printTemplate(Query $query, Request $request)
    {
        $scheduleArray = $mealArray = [];
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

        return $this->render('query/print.html.twig', [
            'query' => $query,
            'countries' => Intl::getRegionBundle()->getCountryNames(),
            'scheduleArray' => $scheduleArray,
            'mealArray' => $mealArray,
            'backUrl' => $request->server->get('HTTP_REFERER'),
        ]);
    }

    /**
     * @param Query $query
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function printTemplateOld(Query $query)
    {
//        $entityManager = $this->getDoctrine()->getManager();
        $countries = Intl::getRegionBundle()->getCountryNames();
        $scheduleArray = $mealArray = [];

        if ($schedules = $this->getDoctrine()->getManager()->getRepository(Schedule::class)->findBy([
            'account' => $query->getAccount(),
            'deleted' => null,
        ], [
            'date' => 'ASC',
            'timeFrom' => 'ASC',
        ])
        ) {
            foreach ($schedules as $schedule) {
//                $scheduleArray[$schedule->getDate()->format('Y-m-d')][] = $schedule;

                $f = $t = $p = 0;
                if ($schedule->getTimeFrom()) {
                    $f = $schedule->getTimeFrom()->format("Hi");
                }

                if ($schedule->getTimeTo()) {
                    $t = $schedule->getTimeTo()->format("Hi");
                }

                if ($schedule->getParts()) {
                    $p = $schedule->getParts();
                }

                $scheduleArray[$schedule->getFacility() . 'X' . $f . 'X' . $t . 'X' . $p]['dates'][] = $schedule->getDate();
                $scheduleArray[$schedule->getFacility() . 'X' . $f . 'X' . $t . 'X' . $p]['facility'] = $schedule->getFacility();
                $scheduleArray[$schedule->getFacility() . 'X' . $f . 'X' . $t . 'X' . $p]['from'] = $schedule->getTimeFrom()->format("H:i");
                $scheduleArray[$schedule->getFacility() . 'X' . $f . 'X' . $t . 'X' . $p]['to'] = $schedule->getTimeTo()->format("H:i");
                $scheduleArray[$schedule->getFacility() . 'X' . $f . 'X' . $t . 'X' . $p]['parts'] = Facility::PARTS[$schedule->getFacility()->getType()][$schedule->getParts()];
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
                $b = $l = $d = $m = $be = $le = $de = $me = 0;
                if ($meal->getBreakfastTime()) {
                    $b = $meal->getBreakfastTime()->format("Hi");
                }
                if ($meal->getBreakfastTimeEnd()) {
                    $be = $meal->getBreakfastTimeEnd()->format("Hi");
                }

                if ($meal->getLunchTime()) {
                    $l = $meal->getLunchTime()->format("Hi");
                }

                if ($meal->getLunchTimeEnd()) {
                    $le = $meal->getLunchTimeEnd()->format("Hi");
                }

                if ($meal->getDinnerTime()) {
                    $d = $meal->getDinnerTime()->format("Hi");
                }

                if ($meal->getDinnerTimeEnd()) {
                    $de = $meal->getDinnerTimeEnd()->format("Hi");
                }

                if ($meal->getMiddleBreakfastTime()) {
                    $m = $meal->getMiddleBreakfastTime()->format("Hi");
                }

                if ($meal->getMiddleBreakfastTimeEnd()) {
                    $me = $meal->getMiddleBreakfastTimeEnd()->format("Hi");
                }

                $mealArray[$meal->getRestaurant() . 'X' . $b . $be . 'X' . $l . $le . 'X' . $d . $de . 'X' . $m . $me]['dates'][] = $meal->getDate();
                $mealArray[$meal->getRestaurant() . 'X' . $b . $be . 'X' . $l . $le . 'X' . $d . $de . 'X' . $m . $me]['restaurant'] = Query::RESTAURANTS[$meal->getRestaurant()];
                $mealArray[$meal->getRestaurant() . 'X' . $b . $be . 'X' . $l . $le . 'X' . $d . $de . 'X' . $m . $me]['breakfast'] = $meal->getBreakfastTime() ? $meal->getBreakfastTime()->format("H:i") . ($meal->getBreakfastTimeEnd() ? '-' . $meal->getBreakfastTimeEnd()->format("H:i") : '') : '-';
                $mealArray[$meal->getRestaurant() . 'X' . $b . $be . 'X' . $l . $le . 'X' . $d . $de . 'X' . $m . $me]['lunch'] = $meal->getLunchTime() ? $meal->getLunchTime()->format("H:i") . ($meal->getLunchTimeEnd() ? '-' . $meal->getLunchTimeEnd()->format("H:i") : '') : '-';
                $mealArray[$meal->getRestaurant() . 'X' . $b . $be . 'X' . $l . $le . 'X' . $d . $de . 'X' . $m . $me]['dinner'] = $meal->getDinnerTime() ? $meal->getDinnerTime()->format("H:i") . ($meal->getDinnerTimeEnd() ? '-' . $meal->getDinnerTimeEnd()->format("H:i") : '') : '-';
                $mealArray[$meal->getRestaurant() . 'X' . $b . $be . 'X' . $l . $le . 'X' . $d . $de . 'X' . $m . $me]['middle'] = $meal->getMiddleBreakfastTime() ? $meal->getMiddleBreakfastTime()->format("H:i") . ($meal->getMiddleBreakfastTimeEnd() ? '-' . $meal->getMiddleBreakfastTimeEnd()->format("H:i") : '') : '-';
            }
        }

        return $this->render('query/print.html.twig', [
            'query' => $query,
            'countries' => $countries,
            'scheduleArray' => $scheduleArray,
            'mealArray' => $mealArray,
        ]);


    }
}
