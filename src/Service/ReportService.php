<?php

namespace App\Service;

use App\Entity\Schedule;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;

class ReportService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ReportService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Form $form
     * @return array
     */
    public function freeLanes(Form $form)
    {
        $schedule = $this->entityManager->getRepository(Schedule::class)->getFreeLanesSchedule($form->get('from')->getData(), $form->get('to')->getData());
        $free = [];
        $default = [
            "07" => 8,
            "08" => 8,
            "09" => 8,
            "10" => 8,
            "11" => 8,
            "12" => 8,
            "13" => 8,
            "14" => 8,
            "15" => 8,
            "16" => 8,
            "17" => 8,
            "18" => 8,
            "19" => 8,
            "20" => 8,
        ];

        $dates = $this->entityManager->getRepository(Schedule::class)->getDatesBetween($form->get('from')->getData(), $form->get('to')->getData());
        //set default 8 lanes to all days and hours
        foreach ($dates as $date) {
            $free[1][$date->format('Y-m-d')] = $default;
            $free[3][$date->format('Y-m-d')] = $default;
        }

        /** @var Schedule $event */
        foreach ($schedule as $key => $event) {
            $lanes = unserialize($event->getLanes());
            if ($lanes) {
                foreach ($default as $timeFrom => $lanesCount) {
                    if ($timeFrom >= $event->getTimeFrom()->format("H") && $timeFrom < $event->getTimeTo()->format("H")) {
                        $free[$event->getFacility()->getId()][$event->getDate()->format('Y-m-d')][$timeFrom] -= count($lanes);
                    }
                }
            }
        }
        return $free;
    }
}