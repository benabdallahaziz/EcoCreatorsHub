<?php

namespace App\Repository;

use App\Entity\CreationJournal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class CreationJournalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreationJournal::class);
    }

  

    /**
     * @return CreationJournal[] 
     */
    public function findPublishedJournals(int $offset = 0, int $limit = 12): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.isPublished = :published')
            ->setParameter('published', true)
            ->orderBy('c.date', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countPublished(): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.isPublished = :published')
            ->setParameter('published', true)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @return CreationJournal[]
     */
    public function searchPublishedJournals(string $query, int $offset = 0, int $limit = 12): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.isPublished = :published')
            ->andWhere('c.title LIKE :query OR c.description LIKE :query')
            ->setParameter('published', true)
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('c.date', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countSearchResults(string $query): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.isPublished = :published')
            ->andWhere('c.title LIKE :query OR c.description LIKE :query')
            ->setParameter('published', true)
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @return CreationJournal[]
     */
    public function findPublishedByCategory($category, int $offset = 0, int $limit = 12): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.isPublished = :published')
            ->andWhere('c.category = :category')
            ->setParameter('published', true)
            ->setParameter('category', $category)
            ->orderBy('c.date', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countPublishedByCategory($category): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.isPublished = :published')
            ->andWhere('c.category = :category')
            ->setParameter('published', true)
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findPublishedWithFilters(int $offset = 0, int $limit = 12, string $sort = 'recent', $category = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.isPublished = :published')
            ->setParameter('published', true);

        if ($category) {
            $qb->andWhere('c.category = :category')
               ->setParameter('category', $category);
        }

        switch ($sort) {
            case 'popular':
                $qb->leftJoin('App\\Entity\\JournalLike', 'l', 'WITH', 'l.creationJournal = c.id')
                   ->groupBy('c.id')
                   ->orderBy('COUNT(l.id)', 'DESC');
                break;
            case 'oldest':
                $qb->orderBy('c.date', 'ASC');
                break;
            default:
                $qb->orderBy('c.date', 'DESC');
        }

        return $qb->setFirstResult($offset)
                  ->setMaxResults($limit)
                  ->getQuery()
                  ->getResult();
    }

    public function countPublishedWithFilters($category = null): int
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.isPublished = :published')
            ->setParameter('published', true);

        if ($category) {
            $qb->andWhere('c.category = :category')
               ->setParameter('category', $category);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
