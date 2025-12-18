<?php

namespace App\Repository;

use App\Entity\EcoTip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EcoTipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EcoTip::class);
    }

    public function findApprovedByFilters(?string $category = null, string $sort = 'recent', ?string $search = null, int $page = 1, ?int $limit = 8): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.isApproved = true');

        if ($category) {
            $qb->andWhere('e.category = :category')
               ->setParameter('category', $category);
        }

        if ($search && trim($search) !== '') {
            $searchTerm = '%' . trim($search) . '%';
            $qb->andWhere('(e.title LIKE :search OR e.content LIKE :search)')
               ->setParameter('search', $searchTerm);
        }

        switch ($sort) {
            case 'popular':
                $qb->orderBy('e.votes', 'DESC');
                break;
            case 'oldest':
                $qb->orderBy('e.createdAt', 'ASC');
                break;
            default:
                $qb->orderBy('e.createdAt', 'DESC');
        }

          if ($limit !== null) {
                $qb->setFirstResult(($page - 1) * $limit)
                    ->setMaxResults($limit);
          }

        return $qb->getQuery()->getResult();
    }

    public function countApprovedByFilters(?string $category = null, ?string $search = null): int
    {
        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.isApproved = true');

        if ($category) {
            $qb->andWhere('e.category = :category')
               ->setParameter('category', $category);
        }

        if ($search && trim($search) !== '') {
            $searchTerm = '%' . trim($search) . '%';
            $qb->andWhere('(e.title LIKE :search OR e.content LIKE :search)')
               ->setParameter('search', $searchTerm);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}