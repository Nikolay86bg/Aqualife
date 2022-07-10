<?php

namespace App\Repository;

use App\Entity\MealSchedule;
use App\Entity\Query;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ScheduleRepository
 * @package App\Repository
 */
class MealScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MealSchedule::class);
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param int|null $restaurant
     * @return mixed
     */
    public function getSchedule(\DateTime $from, \DateTime $to, int $restaurant = null)
    {
        $queryBuilder = $this->createQueryBuilder('meal_schedule');
        $queryBuilder->join('meal_schedule.account', 'account');
        $queryBuilder->join('account.query', 'query');
        $queryBuilder->andWhere('meal_schedule.date >= :from');
        $queryBuilder->andWhere('meal_schedule.date <= :to');
        $queryBuilder->andWhere('meal_schedule.deleted IS NULL');
        $queryBuilder->andWhere('query.status = :status');
        $queryBuilder->andWhere('query.deletedAt IS NULL');

        if ($restaurant !== null) {
            $queryBuilder->andWhere('meal_schedule.restaurant = :restaurant');
            $queryBuilder->setParameter('restaurant', $restaurant);
        }

        $queryBuilder->setParameter('from', $from->format("Y-m-d"));
        $queryBuilder->setParameter('to', $to->format("Y-m-d"));
        $queryBuilder->setParameter('status', Query::STATUS_ACCEPTED);

        $queryBuilder->orderBy('meal_schedule.date', 'ASC');

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
//            dump($event->getRestaurant());exit;

        if ($event->getBreakfastTime()) {
                if($event->getBreakfastTimeEnd()){
                    $bTime = $event->getBreakfastTime()->format("H:i").'-'.$event->getBreakfastTimeEnd()->format("H:i");
                }else{
                    $bTime = $event->getBreakfastTime()->format("H:i");
                }

                $return[$event->getRestaurant()][$event->getDate()->format("d.m.Y")]['breakfast'][$bTime][] = ['id'=>$event->getAccount()->getId(),'name'=>$event->getAccount()->getName()];// . ' ' . $event->getAccount()->getCountry();
            }
            if ($event->getLunchTime()) {
                if($event->getLunchTimeEnd()){
                    $lTime = $event->getLunchTime()->format("H:i").'-'.$event->getLunchTimeEnd()->format("H:i");
                }else{
                    $lTime = $event->getLunchTime()->format("H:i");
                }
                $return[$event->getRestaurant()][$event->getDate()->format("d.m.Y")]['lunch'][$lTime][] =  ['id'=>$event->getAccount()->getId(),'name'=>$event->getAccount()->getName()];// . ' ' . $event->getAccount()->getCountry();
            }
            if ($event->getDinnerTime()) {
                if($event->getDinnerTimeEnd()){
                    $dTime = $event->getDinnerTime()->format("H:i").'-'.$event->getDinnerTimeEnd()->format("H:i");
                }else{
                    $dTime = $event->getDinnerTime()->format("H:i");
                }
                $return[$event->getRestaurant()][$event->getDate()->format("d.m.Y")]['dinner'][$dTime][] =  ['id'=>$event->getAccount()->getId(),'name'=>$event->getAccount()->getName()];// . ' ' . $event->getAccount()->getCountry();
            }
            if ($event->getMiddleBreakfastTime()) {
                if($event->getMiddleBreakfastTimeEnd()){
                    $mbTime = $event->getMiddleBreakfastTime()->format("H:i").'-'.$event->getMiddleBreakfastTimeEnd()->format("H:i");
                }else{
                    $mbTime = $event->getMiddleBreakfastTime()->format("H:i");
                }
                $return[$event->getRestaurant()][$event->getDate()->format("d.m.Y")]['middleBreakfast'][$mbTime][] =  ['id'=>$event->getAccount()->getId(),'name'=>$event->getAccount()->getName()];// . ' ' . $event->getAccount()->getCountry();
            }
        }

        return $this->sortArrayByKey($return);
    }


    /**
     * @param $return
     * @return mixed
     */
    private function sortArrayByKey($return)
    {
        foreach ($return as $restaurant => $dates) {
            foreach ($dates as $date => $meals) {
                foreach ($meals as $meal => $events) {
                ksort($events, SORT_ASC);
                $return[$restaurant][$date][$meal] = $events;
                }
            }
        }

        return $return;
    }

}
