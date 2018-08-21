<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Facility;
use App\Entity\MealSchedule;
use App\Entity\Query;
use App\Entity\Schedule;
use App\Form\AccountType;
use App\Form\QueryFilterType;
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

        $scheduleRepo = $entityManager->getRepository('App:Schedule');

        $queries = $entityManager->getRepository('App:Query')->getListQuery($filter, $sort, $order);

        $queries = (new Paginator($queries))
            ->setEntityManager($this->getDoctrine()->getManager())
            ->paginate($request->query->get('page'));
        $countries = Intl::getRegionBundle()->getCountryNames();

        return $this->render('query/index.html.twig', [
//            'filter' => $filter->createView(),
            'queries' => $queries,
            'countries' => $countries,
            'scheduleRepo' => $scheduleRepo
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response.
     */
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function new(Request $request)
    {

        //TODO
        //Button za celiqt period do tozi za add date za Meal i Facility
        //Facility schedule 4 poleta za 4as

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
                        $schedule = new Schedule();
                        $schedule->setDate((\DateTime::createFromFormat("d/m/Y", $value)));
                        $schedule->setFacility($facilityReference);
                        $schedule->setParts($facility['part'][$key]);
                        $schedule->setTimeFrom(\DateTime::createFromFormat('H:i', $facility['timeFrom'][$key]));
                        $schedule->setTimeTo(\DateTime::createFromFormat('H:i', $facility['timeTo'][$key]));
                        $schedule->setAccount($account);

                        $em->persist($schedule);
                    }
                }
            }

            foreach ($request->get('meals')['date'] as $key => $value) {
                if (!empty($value)) {
                    $mealSchedule = new MealSchedule();
                    $mealSchedule->setDate((\DateTime::createFromFormat("d/m/Y", $value)));
                    $mealSchedule->setAccount($account);

                    if($request->get('meals')['restaurant'][$key] != ""){
                        $mealSchedule->setRestaurant($request->get('meals')['restaurant'][$key]);
                    }
                    if($request->get('meals')['breakfast'][$key] != ""){
                        $mealSchedule->setBreakfastTime(\DateTime::createFromFormat('H:i', $request->get('meals')['breakfast'][$key]));
                    }
                    if($request->get('meals')['middle_breakfast'][$key] != ""){
                        $mealSchedule->setMiddleBreakfastTime(\DateTime::createFromFormat('H:i', $request->get('meals')['middle_breakfast'][$key]));
                    }
                    if($request->get('meals')['lunch'][$key] != ""){
                        $mealSchedule->setLunchTime(\DateTime::createFromFormat('H:i', $request->get('meals')['lunch'][$key]));
                    }
                    if($request->get('meals')['dinner'][$key] != ""){
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

    /**
     * @param Query $query
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function reject(Query $query)
    {
        $query->setStatus(Query::STATUS_REJECTED);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('query_index');
    }

    /**
     * @param Request $request
     * @param Query $query
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function accept(Request $request, Query $query)
    {
        $em = $this->getDoctrine()->getManager();
        $query->setStatus(Query::STATUS_ACCEPTED);

        if($request->request->has('lanes')){
            foreach($request->request->get('lanes') as $scheduleId => $lanes){
                $schedule = $em->getRepository('App:Schedule')->findOneBy([
                    'id' => $scheduleId
                ]);
                $schedule->setLanes(serialize($lanes));
            }
        }

        $em->flush();

        return $this->redirectToRoute('query_index');
    }
}
