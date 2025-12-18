<?php

namespace App\Repository;

use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Artist>
 */
class ArtistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artist::class);
    }

    public function getStatistics(): array
    {
        $currentYear = (int) date('Y');

        $totalArtists = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $certifiedArtists = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.isCertified = true')
            ->getQuery()
            ->getSingleScalarResult();

        $certifiedPercentage = $totalArtists > 0 ? ($certifiedArtists / $totalArtists) * 100 : 0;

        return [
            'total_artists' => (int) $totalArtists,
            'certified_artists' => (int) $certifiedArtists,
            'certified_percentage' => $certifiedPercentage,
        ];
    }

    // Autres méthodes de pagination ou statistiques si besoin
}
