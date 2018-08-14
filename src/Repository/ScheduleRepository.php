<?php

namespace App\Repository;

use App\Entity\Facility;
use App\Entity\Query;
use App\Entity\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
     * @param Form $form
     * @param null $sort
     * @param null $order
     * @return \Doctrine\ORM\Query
     */
    public function getListQuery(Form $form, $sort = null, $order = null)
    {
        $queryBuilder = $this->createQueryBuilder('schedule');

//        if (null !== $form->get('name')->getData()) {
//            $queryBuilder->andWhere('facility.name LIKE :name');
//            $queryBuilder->setParameter('name', $form->get('name')->getData().'%');
//        }

        $queryBuilder->orderBy('schedule.date', $order);

//        if (null !== $sort && null !== $order) {
//            switch ($sort) {
//                case 'id' == $sort:
//                    $sortBy = 'schedule.id';
//                    break;
//
//                case 'timeFrom' == $sort:
//                    $sortBy = 'schedule.timeFrom';
//                    break;
//
//                case 'timeTo' == $sort:
//                    $sortBy = 'schedule.timeTo';
//                    break;
//
//                default:
//                    $queryBuilder->orderBy('schedule.date');
//            }
//
//            $queryBuilder->orderBy($sortBy, $order);
//        }

        return $queryBuilder->getQuery();
    }

    /**
     * @param array $schedule
     * @return array
     */
    public function orderByDate(array $schedule)
    {
        $array = [];
        /** @var Schedule $event */
        foreach($schedule as $event){
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
        $queryBuilder->andWhere('queries.status = :status');
        $queryBuilder->join('schedule.account','account');
        $queryBuilder->join('account.queries','queries');
        $queryBuilder->setParameter('status',Query::STATUS_ACCEPTED);
        $queryBuilder->andWhere('schedule.date = :date');

        $queryBuilder->setParameter('date', $array['date']->format("Y-m-d"));
        $queryBuilder->andWhere('
            (
            (schedule.timeFrom < :from AND :from < schedule.timeTo) OR
            (schedule.timeFrom < :to AND :to < schedule.timeTo) OR
            (:from < schedule.timeFrom AND schedule.timeFrom < :to) OR
            (:from < schedule.timeTo AND schedule.timeTo < :to)
            )
        ');

        $queryBuilder->setParameter('from', $array['from']->format("H:i:s"));
        $queryBuilder->setParameter('to', $array['to']->format("H:i:s"));

        return $queryBuilder->getQuery()->getResult();
    }
}
