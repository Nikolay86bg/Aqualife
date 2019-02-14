<?php

namespace App\Controller;

use App\Entity\Facility;
use App\Entity\Schedule;
use App\Form\ScheduleFilterType;
use App\Service\ColorService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    public function indexOld(Request $request)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_LIST_ROLE);

        $filter = $this->createForm(ScheduleFilterType::class);
        $filter->handleRequest($request);

        if ($filter->isSubmitted() && $filter->isValid()) {
            $facility = $filter->get('facility')->getData();
            $date = $filter->get('date')->getData();
        }else{
            $entityManager = $this->getDoctrine()->getManager();
            $facility = $entityManager->getRepository('App:Facility')->findOneBy(['id' => 1]);
            $date = new \DateTime();
        }

        return $this->render('schedule/index_old.html.twig', [
            'filter' => $filter->createView(),
            'facility' => $facility,
            'date' => $date,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $filter = $this->createForm(ScheduleFilterType::class);
        $filter->handleRequest($request);

        if ($filter->isSubmitted() && $filter->isValid()) {
            $facility = $filter->get('facility')->getData();
            $date = $filter->get('date')->getData();
        }else{
            $entityManager = $this->getDoctrine()->getManager();
            $facility = $entityManager->getRepository('App:Facility')->findOneBy(['id' => 1]);
            $date = new \DateTime();
        }

        return $this->render('schedule/index.html.twig', [
            'filter' => $filter->createView(),
            'facility' => $facility,
            'date' => $date,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function feed(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $facility = $entityManager->getRepository(Facility::class)->findOneBy([
            'id' => $request->request->get('facility_id')
        ]);

        $start = \DateTime::createFromFormat ( 'Y-m-d\TH:i:s' ,  $request->request->get('start'));
        $end = \DateTime::createFromFormat ( 'Y-m-d\TH:i:s' ,  $request->request->get('end'));

        $schedule = $entityManager->getRepository(Schedule::class)->getSchedule($facility, $start, $end);
        $schedule = $entityManager->getRepository(Schedule::class)->prepareSchedule($schedule, $this->get(ColorService::class));

        return new JsonResponse($schedule);
    }

    /**
     * @param Facility $facility
     * @param \DateTime $from
     * @param \DateTime $until
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function printTemplate(Facility $facility, \DateTime $from, \DateTime $until)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $schedule = $entityManager->getRepository(Schedule::class)->getSchedule($facility, $from, $until);
        $scheduleOrdered = $entityManager->getRepository(Schedule::class)->preparePrintSchedule($schedule);

        return $this->render('schedule/print.html.twig', [
            'scheduleOrdered' => $scheduleOrdered,
            'facility' => $facility,
            'from' => $from,
            'until' => $until,
        ]);
    }

}
