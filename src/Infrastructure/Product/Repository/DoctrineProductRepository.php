<?php

namespace App\Infrastructure\Product\Repository;

use App\Domain\Common\Repository\PaginatedResult;
use App\Domain\Common\Repository\Pagination;
use App\Domain\Product\Model\Product;
use App\Domain\Product\Repository\ProductFilters;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Product::class);
    }

    public function add(Product $product): void
    {
        $this->_em->persist($product);
    }

    public function findPaginatedByFilters(ProductFilters $filters, Pagination $pagination): PaginatedResult
    {
        $queryBuilder = $this->createQueryBuilder('p');
        if ($filters->taxonomy()) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq('p.taxonomy', ':taxonomy'))
                ->setParameter('taxonomy', $filters->taxonomy()->uuid());
        }
        if ($filters->minimumPrice()) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->gte('p.price', ':minimumPrice'))
                ->setParameter('minimumPrice', $filters->minimumPrice());
        }
        if ($filters->maximumPrice()) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->lte('p.price', ':maximumPrice'))
                ->setParameter('maximumPrice', $filters->maximumPrice());
        }
        if ($filters->text()) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('p.name', ':name'),
                    $queryBuilder->expr()->like('p.description', ':description')
                ))
                ->setParameter('name', '%' . addcslashes($filters->text(), '%_') . '%')
                ->setParameter('description', '%' . addcslashes($filters->text(), '%_') . '%');
        }

        $queryBuilder->setFirstResult(($pagination->page() - 1) * $pagination->itemsPerPage());
        $queryBuilder->setMaxResults($pagination->itemsPerPage());

        $query = $queryBuilder->getQuery();
        $paginator = new Paginator($query);

        return new PaginatedResult(
            $pagination->page(),
            $pagination->itemsPerPage(),
            $paginator->count(),
            $query->getResult()
        );
    }
}