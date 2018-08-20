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


}
