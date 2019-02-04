<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Query;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Form;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @param Form $form
     * @param null $sort
     * @param null $order
     * @return \Doctrine\ORM\Query
     */
    public function getListQuery(Form $form, $sort = null, $order = null)
    {
        $queryBuilder = $this->createQueryBuilder('account');

        if (null !== $form->get('name')->getData()) {
            $queryBuilder->andWhere('account.name LIKE :name');
            $queryBuilder->setParameter('name', $form->get('name')->getData().'%');
        }

        if (null !== $sort && null !== $order) {
            switch ($sort) {
                case 'id' == $sort:
                    $sortBy = 'account.id';
                    break;

                case 'name' == $sort:
                    $sortBy = 'account.name';
                    break;

                case 'agent' == $sort:
                    $sortBy = 'account.agent';
                    break;

                case 'sport' == $sort:
                    $sortBy = 'account.sport';
                    break;


                case 'country' == $sort:
                    $sortBy = 'account.country';
                    break;

                default:

                    $queryBuilder->orderBy('account.id');
            }

            $queryBuilder->orderBy($sortBy, $order);
        }

        $queryBuilder
            ->andWhere('account.deletedAt IS NULL');

        return $queryBuilder->getQuery();
    }

    /**
     * @return mixed
     */
    public function getCurrentAccounts()
    {
        $queryBuilder = $this->createQueryBuilder('account');
        $queryBuilder->join('account.query','query')
            ->where('query.status = :status')
            ->andWhere('DATE(query.dateOfArrival) <= DATE(:today)')
            ->andWhere('DATE(query.dateOfDeparture) >= DATE(:today)')
            ->andWhere('query.deletedAt IS NULL')
            ->setParameters([
            'status' => Query::STATUS_ACCEPTED,
            'today' => new \DateTime()
        ]);

        $queryBuilder->orderBy('account.name', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }
}
