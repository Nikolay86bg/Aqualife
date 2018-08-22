<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Query;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Form;

/**
 * Class QueryRepository
 * @package App\Repository
 */
class QueryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Query::class);
    }

    /**
     * @param Form $form
     * @param null $sort
     * @param null $order
     * @return \Doctrine\ORM\Query
     */
    public function getListQuery(Form $form, $sort = null, $order = null)
    {
        $queryBuilder = $this->createQueryBuilder('query');

        if (null !== $form->get('name')->getData()) {
            $queryBuilder->andWhere('query.name LIKE :name');
            $queryBuilder->setParameter('name', $form->get('name')->getData().'%');
        }

        if (null !== $sort && null !== $order) {
            switch ($sort) {
                case 'id' == $sort:
                    $sortBy = 'query.id';
                    break;

                case 'name' == $sort:
                    $sortBy = 'query.name';
                    break;

                case 'manager' == $sort:
                    $sortBy = 'query.manager';
                    break;

                case 'sport' == $sort:
                    $sortBy = 'query.sport';
                    break;

                case 'status' == $sort:
                    $sortBy = 'query.status';
                    break;

                case 'country' == $sort:
                    $sortBy = 'query.country';
                    break;

                case 'arrival' == $sort:
                    $sortBy = 'query.dateOfArrival';
                    break;

                case 'departure' == $sort:
                    $sortBy = 'query.dateOfDeparture';
                    break;

                case 'payed' == $sort:
                    $sortBy = 'query.payed';
                    break;

                default:

                    $queryBuilder->orderBy('query.id');
            }

            $queryBuilder->orderBy($sortBy, $order);
        }

        return $queryBuilder->getQuery();
    }
}
