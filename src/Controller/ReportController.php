<?php

namespace App\Controller;


use App\Entity\Facility;
use App\Form\FreeLanesReportFilterType;
use App\Service\ReportService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class ReportController
 * @package App\Controller
 */
class ReportController extends AbstractController
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
