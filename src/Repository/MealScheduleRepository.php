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

        if ($restaurant) {
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
                $return[$event->getRestaurant()][$event->getDate()->format("Y-m-d")]['breakfast'][$event->getBreakfastTime()->format("H:i")][] = ['id'=>$event->getAccount()->getId(),'name'=>$event->getAccount()->getName()];// . ' ' . $event->getAccount()->getCountry();
            }
            if ($event->getLunchTime()) {
                $return[$event->getRestaurant()][$event->getDate()->format("Y-m-d")]['lunch'][$event->getLunchTime()->format("H:i")][] =  ['id'=>$event->getAccount()->getId(),'name'=>$event->getAccount()->getName()];// . ' ' . $event->getAccount()->getCountry();
            }
            if ($event->getDinnerTime()) {
                $return[$event->getRestaurant()][$event->getDate()->format("Y-m-d")]['dinner'][$event->getDinnerTime()->format("H:i")][] =  ['id'=>$event->getAccount()->getId(),'name'=>$event->getAccount()->getName()];// . ' ' . $event->getAccount()->getCountry();
            }
            if ($event->getMiddleBreakfastTime()) {
                $return[$event->getRestaurant()][$event->getDate()->format("Y-m-d")]['middleBreakfast'][$event->getMiddleBreakfastTime()->format("H:i")][] =  ['id'=>$event->getAccount()->getId(),'name'=>$event->getAccount()->getName()];// . ' ' . $event->getAccount()->getCountry();
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
        foreach ($return as $date => $meals) {
            foreach ($meals as $meal => $events) {
                ksort($events, SORT_ASC);
                $return[$date][$meal] = $events;
            }
        }

        return $return;
    }

}
