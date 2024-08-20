<?php

namespace App\Repository;

use App\Entity\Facility;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Form;

/**
 * Class FacilityRepository
 * @package App\Repository
 */
class FacilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facility::class);
    }

    /**
     * @param Form $form
     * @param null $sort
     * @param null $order
     * @return \Doctrine\ORM\Query
     */
    public function getListQuery(Form $form, $sort = null, $order = null)
    {
        $queryBuilder = $this->createQueryBuilder('facility');

        $queryBuilder->where('facility.isActive = 1');

        if (null !== $form->get('name')->getData()) {
            $queryBuilder->andWhere('facility.name LIKE :name');
            $queryBuilder->setParameter('name', $form->get('name')->getData().'%');
        }

        if (null !== $sort && null !== $order) {
            switch ($sort) {
                case 'id' == $sort:
                    $sortBy = 'facility.id';
                    break;

                case 'name' == $sort:
                    $sortBy = 'facility.name';
                    break;

                case 'type' == $sort:
                    $sortBy = 'facility.type';
                    break;

                default:

                    $queryBuilder->orderBy('facility.id');
            }

            $queryBuilder->orderBy($sortBy, $order);
        }

        return $queryBuilder->getQuery();
    }

    /**
     * @param int $id
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNameById(int $id)
    {
        $queryBuilder = $this->createQueryBuilder('facility');
        $queryBuilder
            ->select('facility.name')
            ->andWhere('facility.id = :id')
            ->setParameter('id', $id);

        return $queryBuilder->getQuery()->getSingleResult();
    }
}
