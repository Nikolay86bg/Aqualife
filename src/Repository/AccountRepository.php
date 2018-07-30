<?php

namespace App\Repository;

use App\Entity\Account;
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
                case 'userId' == $sort:
                    $sortBy = 'account.id';
                    break;

                case 'user' == $sort:
                    $sortBy = 'account.firstName';
                    break;

                case 'username' == $sort:
                    $sortBy = 'account.username';
                    break;

                case 'email' == $sort:
                    $sortBy = 'account.email';
                    break;


                case 'createdAt' == $sort:
                    $sortBy = 'account.createdAt';
                    break;

                case 'updatedAt' == $sort:
                    $sortBy = 'account.updatedAt';
                    break;

                default:

                    $queryBuilder->orderBy('account.id');
            }

            $queryBuilder->orderBy($sortBy, $order);
        }

        return $queryBuilder->getQuery();
    }
}
