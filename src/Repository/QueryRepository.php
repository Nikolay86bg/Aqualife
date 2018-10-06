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

        if (null !== $form->get('status')->getData()) {
            $queryBuilder
                ->andWhere('query.status LIKE :status')
                ->setParameter('status', $form->get('status')->getData());
        }
        if (null !== $form->get('sport')->getData()) {
            $queryBuilder->join('query.account', 'account')
                ->andWhere('account.sport LIKE :sport')
                ->setParameter('sport', $form->get('sport')->getData() . '%');
        }
        if (null !== $form->get('country')->getData()) {
            $queryBuilder->join('query.account', 'account')
                ->andWhere('account.country LIKE :country')
                ->setParameter('country', $form->get('country')->getData() . '%');
        }
        if (null !== $form->get('from')->getData()) {
            $queryBuilder
                ->andWhere('DATE(query.dateOfArrival) >= :from')
                ->setParameter('from', $form->get('from')->getData());
        }
        if (null !== $form->get('to')->getData()) {
            $queryBuilder
                ->andWhere('DATE(query.dateOfDeparture) <= :to')
                ->setParameter('to', $form->get('to')->getData());
        }

        $queryBuilder
            ->andWhere('query.deletedAt IS NULL');

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
