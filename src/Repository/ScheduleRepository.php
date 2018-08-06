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

        if (null !== $sort && null !== $order) {
            switch ($sort) {
                case 'id' == $sort:
                    $sortBy = 'schedule.id';
                    break;

                case 'timeFrom' == $sort:
                    $sortBy = 'schedule.timeFrom';
                    break;

                case 'timeTo' == $sort:
                    $sortBy = 'schedule.timeTo';
                    break;

                default:

                    $queryBuilder->orderBy('schedule.id');
            }

            $queryBuilder->orderBy($sortBy, $order);
        }

        return $queryBuilder->getQuery();
    }
}
