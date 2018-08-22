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
        $queryBuilder->andWhere('meal_schedule.restaurant = :restaurant');
        $queryBuilder->andWhere('meal_schedule.date >= :from');
        $queryBuilder->andWhere('meal_schedule.date <= :to');

        $queryBuilder->setParameter('restaurant', $restaurant);
        $queryBuilder->setParameter('from', $from->format("Y-m-d"));
        $queryBuilder->setParameter('to', $to->format("Y-m-d"));

        return $queryBuilder->getQuery()->getResult();
    }

    public function prepareSchedule(array $schedule)
    {
        $return = $array = [];
        /** @var Schedule $event */
        foreach ($schedule as $event) {
            if ($event->getAccount()->getQuery()->getStatus() == Query::STATUS_ACCEPTED) {
                if ($event->getFacility()->getType() == Facility::TYPE_POOL) {
                    $lanes = unserialize($event->getLanes());
                    foreach ($lanes as $lane => $on) {
                        array_push($return, $this->setScheduleArray($event, $lane, 'green'));
                    }
                } else {
                    array_push($return, $this->setScheduleArray($event,  Facility::PARTS[$event->getFacility()->getType()][$event->getParts()], 'green'));
                }
            } else {
                array_push($return, $this->setScheduleArray($event, 'Неодобрени', 'red', true));
            }
        }

        return $return;
    }

    private function setScheduleArray(Schedule $schedule, string $id, string $color)
    {
        $array['id'] = $id;
        $array['resourceId'] = $id;
        $array['start'] = $schedule->getDate()->format("Y-m-d") . 'T' . $schedule->getTimeFrom()->format("H:i:s");
        $array['end'] = $schedule->getDate()->format("Y-m-d") . 'T' . $schedule->getTimeTo()->format("H:i:s");
        $array['title'] = $schedule->getAccount()->getName();
        $array['color'] = $color;


        return $array;
    }


}
