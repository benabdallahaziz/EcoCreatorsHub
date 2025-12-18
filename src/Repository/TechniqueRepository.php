<?php

namespace App\Repository;

use App\Entity\Technique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TechniqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Technique::class);
    }

    public function findByFilters(?string $category = null, ?string $difficulty = null, ?string $search = null, int $page = 1, int $limit = 6): array
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.createdAt', 'DESC');

        if ($category) {
            $qb->andWhere('t.category = :category')
               ->setParameter('category', $category);
        }

        if ($difficulty) {
            $qb->andWhere('t.difficulty = :difficulty')
               ->setParameter('difficulty', $difficulty);
        }

        if ($search && trim($search) !== '') {
            $searchTerm = '%' . trim($search) . '%';
            $qb->andWhere('(t.name LIKE :search OR t.description LIKE :search OR t.materials LIKE :search OR t.steps LIKE :search)')
               ->setParameter('search', $searchTerm);
        }

        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countByFilters(?string $category = null, ?string $difficulty = null, ?string $search = null): int
    {
        $qb = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)');

        if ($category) {
            $qb->andWhere('t.category = :category')
               ->setParameter('category', $category);
        }

        if ($difficulty) {
            $qb->andWhere('t.difficulty = :difficulty')
               ->setParameter('difficulty', $difficulty);
        }

        if ($search && trim($search) !== '') {
            $searchTerm = '%' . trim($search) . '%';
            $qb->andWhere('(t.name LIKE :search OR t.description LIKE :search OR t.materials LIKE :search OR t.steps LIKE :search)')
               ->setParameter('search', $searchTerm);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}