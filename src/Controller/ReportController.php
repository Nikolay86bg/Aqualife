<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Facility;
use App\Entity\MealSchedule;
use App\Entity\Query;
use App\Entity\Schedule;
use App\Form\AccountType;
use App\Form\FreeLanesReportFilterType;
use App\Form\QueryFilterType;
use App\Security\Voter\QueryVoter;
use App\Service\ReportService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Intl\Intl;
use App\Service\MailerService;

/**
 * Class ReportController
 * @package App\Controller
 */
class ReportController extends Controller
{
    /**
     * @var ReportService
     */
    private $reportService;

    /**
     * ReportController constructor.
     * @param ReportService $reportService
     */
    function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function freeLanes(Request $request)
    {
        $filter = $this->createForm(FreeLanesReportFilterType::class);
        $filter->handleRequest($request);

        $report = [];
        $lanesNeeded = 8;
        if ($filter->isSubmitted() && $filter->isValid()) {
            $report = $this->reportService->freeLanes($filter);
            $lanesNeeded = $filter->get('lanesNeeded')->getData();
        }

        return $this->render('report/index.html.twig', [
            'filter' => $filter->createView(),
            'report' => $report,
            'lanesNeeded' => $lanesNeeded,
            'facilityRepo' => $this->getDoctrine()->getManager()->getRepository(Facility::class)
        ]);
    }


}
