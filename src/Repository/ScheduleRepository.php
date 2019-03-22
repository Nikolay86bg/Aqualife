<?php

namespace App\Repository;

use App\Entity\Facility;
use App\Entity\Query;
use App\Entity\Schedule;
use App\Service\ColorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Query\QueryException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Form;

/**
 * Class ScheduleRepository
 * @package App\Repository
 */
class ScheduleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    /**
     * @param array $schedule
     * @return array
     */
    public function orderByDate(array $schedule)
    {
        $array = [];
        /** @var Schedule $event */
        foreach ($schedule as $event) {
            $array[$event->getDate()->format("Y-m-d")][] = $event;
        }

        return $array;
    }

    /**
     * @param $from
     * @param $to
     * @return \DatePeriod
     * @throws \Exception
     */
    public function getDatesBetween(\DateTime $from, \DateTime $to)
    {
        $periodArray = [];

        $to->modify('+1 day');

        $period = new \DatePeriod(
            $from,
            new \DateInterval('P1D'),
            $to
        );

        foreach ($period as $key => $value) {
            $periodArray[] = $value;
        }

        return $periodArray;
    }

    /**
     * @param $array
     * @return mixed
     */
    public function getReservedLanes($array)
    {
        $queryBuilder = $this->createQueryBuilder('schedule');
        $queryBuilder->join('schedule.account', 'account');
        $queryBuilder->join('account.query', 'query');
        $queryBuilder->andWhere('query.status = :status');
        $queryBuilder->setParameter('status', Query::STATUS_ACCEPTED);
        $queryBuilder->andWhere('schedule.date = :date');
        $queryBuilder->andWhere('schedule.deleted IS NULL');
        $queryBuilder->andWhere('query.deletedAt IS NULL');
        $queryBuilder->andWhere('schedule.facility = :facility');

        $queryBuilder->setParameter('date', $array['date']->format("Y-m-d"));
        $queryBuilder->andWhere('
         (
             (
                (:from <= schedule.timeFrom AND schedule.timeFrom < :to ) OR (:from < schedule.timeTo AND schedule.timeTo <= :to )
             )
             OR
             ( schedule.timeFrom < :from AND schedule.timeTo > :to )
         )
        ');

//        (
//        (:from <= schedule.timeFrom AND schedule.timeTo <= :to AND schedule.timeFrom < :to )
//            )

//        (schedule.timeFrom < :from AND :from < schedule.timeTo) OR
//            (schedule.timeFrom < :to AND :to < schedule.timeTo) OR
//            (:from < schedule.timeFrom AND schedule.timeFrom < :to) OR
//            (:from <= schedule.timeTo AND schedule.timeTo <= :to)

        $queryBuilder->setParameter('from', $array['from']->format("H:i:s"));
        $queryBuilder->setParameter('to', $array['to']->format("H:i:s"));
        $queryBuilder->setParameter('facility', $array['facility']);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Facility $facility
     * @param \DateTime $from
     * @param \DateTime $to
     * @return mixed
     */
    public function getSchedule(Facility $facility, \DateTime $from, \DateTime $to)
    {
        $queryBuilder = $this->createQueryBuilder('schedule');
        $queryBuilder->join('schedule.account', 'account');
        $queryBuilder->join('account.query', 'query');
        $queryBuilder->andWhere('schedule.facility = :facility');
        $queryBuilder->andWhere('schedule.date >= :from');
        $queryBuilder->andWhere('schedule.date <= :to');
        $queryBuilder->andWhere('schedule.date <= :to');
        $queryBuilder->andWhere('schedule.deleted IS NULL');
        $queryBuilder->andWhere('query.deletedAt IS NULL');
        $queryBuilder->andWhere('query.status != :status');

        $queryBuilder->setParameter('facility', $facility);
        $queryBuilder->setParameter('from', $from->format("Y-m-d"));
        $queryBuilder->setParameter('to', $to->format("Y-m-d"));
        $queryBuilder->setParameter('status', Query::STATUS_REJECTED);

        $queryBuilder->orderBy('schedule.date', 'ASC');
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param array $schedule
     * @return array
     */
    public function prepareSchedule(array $schedule, ColorService $colorService)
    {
        $return = $array = [];
        /** @var Schedule $event */
        foreach ($schedule as $event) {
            if ($event->getAccount()->getQuery()->getStatus() == Query::STATUS_ACCEPTED) {
                if ($event->getFacility()->getType() == Facility::TYPE_POOL) {
                    $lanes = unserialize($event->getLanes());
                    if ($lanes) {
                        foreach ($lanes as $lane => $on) {
                            array_push($return, $this->setScheduleArray($event, $lane, $colorService->getColorNameFromId($event->getAccount()->getId()), true));
                        }
                    }
                } else {
                    $Iid = Facility::PARTS[$event->getFacility()->getType()][$event->getParts()];
                    if($Iid == 'All'){
                        array_push($return, $this->setScheduleArray($event, '1/2 A', $colorService->getColorNameFromId($event->getAccount()->getId()), true));
                        array_push($return, $this->setScheduleArray($event, '1/2 B', $colorService->getColorNameFromId($event->getAccount()->getId()), true));
                    }else{
                        array_push($return, $this->setScheduleArray($event, $Iid, $colorService->getColorNameFromId($event->getAccount()->getId()), true));
                    }
                }
            } else {
                array_push($return, $this->setScheduleArray($event, 'Неодобрени', 'red', true));
            }
        }

        return $return;
    }

    /**
     * @param Schedule $schedule
     * @param $id
     * @param $color
     * @param bool|false $description
     * @return mixed
     */
    private function setScheduleArray(Schedule $schedule, $id, $color, $description = false)
    {
        $array['id'] = $id;
        $array['resourceId'] = $id;
        $array['start'] = $schedule->getDate()->format("Y-m-d") . 'T' . $schedule->getTimeFrom()->format("H:i:s");
        $array['end'] = $schedule->getDate()->format("Y-m-d") . 'T' . $schedule->getTimeTo()->format("H:i:s");
        $array['title'] = $schedule->getAccount()->getName();
        $array['color'] = $color;
        if($schedule->getAccount()->getQuery()->getPayed()){
            $array['textColor'] = 'black';
            $array['borderColor'] = 'black';
        }


        if ($description) {
            $array['description'] = 'Части: ' . Facility::PARTS[$schedule->getFacility()->getType()][$schedule->getParts()] .
            " | От-До: " .
            $schedule->getTimeFrom()->format("H:i") . " - " . $schedule->getTimeTo()->format("H:i");
        }

        return $array;
    }

    /**
     * @param array $schedule
     * @return array
     */
    public function preparePrintSchedule(array $schedule)
    {
        $return = $array = [];
        /** @var Schedule $event */
        foreach ($schedule as $event) {
            if ($event->getAccount()->getQuery()->getStatus() == Query::STATUS_ACCEPTED) {
                if ($event->getFacility()->getType() == Facility::TYPE_POOL) {
                    $lanes = unserialize($event->getLanes());
                    if ($lanes) {
                        foreach ($lanes as $lane => $on) {
                            $return[$event->getDate()->format('Y-m-d')][$lane][$event->getTimeFrom()->format("H:i")]['desc'] = $event->getTimeFrom()->format("H:i")."-".$event->getTimeTo()->format("H:i")." ".$event->getAccount()->getName();
                            $return[$event->getDate()->format('Y-m-d')][$lane][$event->getTimeFrom()->format("H:i")]['endTime'] = $event->getTimeTo()->format("H:i");
                        }
                    }
                } else {
//                    $Iid = Facility::PARTS[$event->getFacility()->getType()][$event->getParts()];
                    $Iid = $event->getParts();

                    $return[$event->getDate()->format('Y-m-d')][$Iid][$event->getTimeFrom()->format("H:i")]['desc'] = $event->getTimeFrom()->format("H:i")."-".$event->getTimeTo()->format("H:i")." ".$event->getAccount()->getName();
                    $return[$event->getDate()->format('Y-m-d')][$Iid][$event->getTimeFrom()->format("H:i")]['endTime'] = $event->getTimeTo()->format("H:i");
                }
            }
        }

        //Sorting by time
        foreach($return as $date => $arr1){
            foreach($arr1 as $key2 => $arr2){
                ksort($arr2, SORT_ASC);
                $return[$date][$key2] = $arr2;
            }
        }

        return $return;
    }

}
