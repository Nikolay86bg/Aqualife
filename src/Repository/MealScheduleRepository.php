<?php

namespace App\Repository;

use App\Entity\Facility;
use App\Entity\MealSchedule;
use App\Entity\Query;
use App\Entity\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Query\QueryException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Form;

/**
 * Class ScheduleRepository
 * @package App\Repository
 */
class MealScheduleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MealSchedule::class);
    }

    /**
     * @param string $restaurant
     * @param \DateTime $from
     * @param \DateTime $to
     * @return mixed
     */
    public function getSchedule(string $restaurant, \DateTime $from, \DateTime $to)
    {
        $queryBuilder = $this->createQueryBuilder('meal_schedule');
        $queryBuilder->join('meal_schedule.account','account');
        $queryBuilder->join('account.query', 'query');
        $queryBuilder->andWhere('meal_schedule.restaurant = :restaurant');
        $queryBuilder->andWhere('meal_schedule.date >= :from');
        $queryBuilder->andWhere('meal_schedule.date <= :to');
        $queryBuilder->andWhere('query.status = :status');

        $queryBuilder->setParameter('restaurant', $restaurant);
        $queryBuilder->setParameter('from', $from->format("Y-m-d"));
        $queryBuilder->setParameter('to', $to->format("Y-m-d"));
        $queryBuilder->setParameter('status', Query::STATUS_ACCEPTED);

        $queryBuilder->orderBy('meal_schedule.date','ASC');


        return $queryBuilder->getQuery()->getResult();
    }


    /**
     * @param array $schedule
     * @return array
     */
    public function prepareSchedule(array $schedule)
    {
        $return = [];
        /** @var MealSchedule $event */
        foreach ($schedule as $event) {
            if($event->getBreakfastTime()){
                $return[$event->getDate()->format("Y-m-d")]['breakfast'][$event->getBreakfastTime()->format("Hi")][] = $event->getAccount()->getName().' '.$event->getAccount()->getCountry();
            }
            if($event->getLunchTime()) {
                $return[$event->getDate()->format("Y-m-d")]['lunch'][$event->getLunchTime()->format("Hi")][] = $event->getAccount()->getName() . ' ' . $event->getAccount()->getCountry();
            }
            if($event->getDinnerTime()) {
                $return[$event->getDate()->format("Y-m-d")]['dinner'][$event->getDinnerTime()->format("Hi")][] = $event->getAccount()->getName() . ' ' . $event->getAccount()->getCountry();
            }
            if($event->getMiddleBreakfastTime()) {
                $return[$event->getDate()->format("Y-m-d")]['middleBreakfast'][$event->getMiddleBreakfastTime()->format("Hi")][] = $event->getAccount()->getName() . ' ' . $event->getAccount()->getCountry();
            }
        }

        return $this->sortArrayByKey($return);
    }


    /**
     * @param $return
     * @return mixed
     */
    private function sortArrayByKey($return){
        foreach($return as $date => $meals){
            foreach($meals as $meal => $events){
                ksort($events, SORT_ASC);
                $return[$date][$meal] = $events;
            }
        }

        return $return;
    }

}
