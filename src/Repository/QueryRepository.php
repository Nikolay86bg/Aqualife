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
     * @throws \Exception
     */
    public function getListQuery(Form $form, $sort = null, $order = null)
    {
        $queryBuilder = $this->createQueryBuilder('query');
        $queryBuilder->join('query.account', 'account');

        if (null !== $form->get('status')->getData()) {
            $queryBuilder
                ->andWhere('query.status LIKE :status')
                ->setParameter('status', $form->get('status')->getData());
        }
        if (null !== $form->get('sport')->getData()) {
            $queryBuilder
                ->andWhere('account.sport LIKE :sport')
                ->setParameter('sport', $form->get('sport')->getData() . '%');
        }
        if (null !== $form->get('name')->getData()) {
            $queryBuilder
                ->andWhere('account.name LIKE :name')
                ->setParameter('name', $form->get('name')->getData() . '%');
        }
        if (null !== $form->get('country')->getData()) {
            $queryBuilder
                ->andWhere('account.country LIKE :country')
                ->setParameter('country', $form->get('country')->getData() . '%');
        }
        if (null !== $form->get('timeframe')->getData()) {
            $today = (new \DateTime());
            switch ($form->get('timeframe')->getData()) {
                case 0:
                    $queryBuilder
                        ->andWhere('DATE(query.dateOfDeparture) >= DATE(:timeframe)')
                        ->setParameter('timeframe', $today);
                    break;
                case 1:
                    $queryBuilder
                        ->andWhere('DATE(query.dateOfArrival) <= DATE(:timeframe)')
                        ->setParameter('timeframe', $today);
                    break;
                default:
                    break;
            }
        }
        if (null !== $form->get('from')->getData() && null !== $form->get('to')->getData()) {
            $queryBuilder
                ->andWhere('DATE(query.dateOfArrival) >= :from AND DATE(query.dateOfArrival) <= :to')
                ->orWhere('DATE(query.dateOfDeparture) >= :from AND DATE(query.dateOfDeparture) <= :to')
                ->setParameter('from', $form->get('from')->getData())
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
                    $sortBy = 'account.name';
                    break;

                case 'manager' == $sort:
                    $sortBy = 'account.manager';
                    break;

                case 'sport' == $sort:
                    $sortBy = 'account.sport';
                    break;

                case 'status' == $sort:
                    $sortBy = 'query.status';
                    break;

                case 'country' == $sort:
                    $sortBy = 'account.country';
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

                case 'numberOfPeople' == $sort:
                    $sortBy = 'query.numberOfPeople';
                    break;

                default:

                    $sortBy = 'query.dateOfArrival';
            }

            $queryBuilder->orderBy($sortBy, $order);
        }

        return $queryBuilder->getQuery();
    }
}
