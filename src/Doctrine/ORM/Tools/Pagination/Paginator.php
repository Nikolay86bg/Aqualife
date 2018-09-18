<?php

/*
 * (c) 411 Marketing
 */

namespace App\Doctrine\ORM\Tools\Pagination;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as BasePaginator;

class Paginator extends BasePaginator
{
    /**
     * @var int
     */
    protected $page = 1;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @const RESULTS_PER_PAGE
     */
    const RESULTS_PER_PAGE = 20;

    /**
     * Paginator constructor.
     *
     * @param $query
     * @param bool $fetchJoinCollection
     * @param bool $resultsPerPage
     */
    public function __construct($query, $fetchJoinCollection = true, $resultsPerPage = false)
    {
        parent::__construct($query, $fetchJoinCollection);

        if ($resultsPerPage) {
            $this->limit = $resultsPerPage;
        } else {
            //Ensure the default limit is set
            $this->limit = static::RESULTS_PER_PAGE;
        }
    }

    /**
     * @param EntityManagerInterface $entityManager
     *
     * @return Paginator
     */
    public function setEntityManager(EntityManagerInterface $entityManager): self
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return Paginator
     */
    public function paginate($page, $limit = null): self
    {
        $page = max(1, $page);

        if (null !== $limit) {
            $this->limit = $limit;
        }

        $this->page = min($page, $this->getTotalPagesCount());

        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getTotalPagesCount(): int
    {
        return (int) ceil($this->count() / $this->getLimit());
    }

    /**
     * @return bool
     */
    public function canPaginate(): bool
    {
        return 1 < $this->getTotalPagesCount();
    }

    /**
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     *
     * @return \Iterator
     */
    public function getIterator(): \Iterator
    {
        $this->getQuery()->setMaxResults($this->getLimit());
        $this->getQuery()->setFirstResult(($this->getLimit() * ($this->getPage() - 1)));

        foreach (parent::getIterator() as $result) {
            yield $result;

            $this->entityManager->clear();
        }
    }
}
