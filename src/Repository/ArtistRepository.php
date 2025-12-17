<?php
// src/Repository/ArtistRepository.php

namespace App\Repository;

use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Artist>
 */
class ArtistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artist::class);
    }

    /**
     * Paginate results - VERSION CORRIGÉE
     */
    public function paginate(QueryBuilder $queryBuilder, int $page, int $limit): Paginator
    {
        $offset = ($page - 1) * $limit;

        // Important: Cloner le QueryBuilder pour compter sans affecter la requête principale
        $countQueryBuilder = clone $queryBuilder;

        // Supprimer les clauses ORDER BY pour le comptage
        $countQueryBuilder->resetDQLPart('orderBy');

        // Compter le nombre total de résultats
        $countQueryBuilder->select('COUNT(a.id)');
        $totalResults = (int) $countQueryBuilder->getQuery()->getSingleScalarResult();

        // Appliquer la pagination à la requête originale
        $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        // Créer et retourner le Paginator
        $query = $queryBuilder->getQuery();

        return new Paginator($query, true); // true pour fetchJoinCollection
    }

    /**
     * Alternative: Pagination simple qui retourne un tableau
     */
    public function paginateSimple(QueryBuilder $queryBuilder, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();
    }

public function findUsersWithoutArtist(): array
{
    return $this->createQueryBuilder('u')
        ->leftJoin('u.artist', 'a')
        ->where('a.id IS NULL')
        ->getQuery()
        ->getResult();
}

    /**
     * Compter les résultats avec les mêmes filtres
     */
    public function countWithFilters(QueryBuilder $queryBuilder): int
    {
        // Cloner le QueryBuilder pour éviter de modifier l'original
        $countQueryBuilder = clone $queryBuilder;

        // Supprimer les clauses ORDER BY
        $countQueryBuilder->resetDQLPart('orderBy');

        // Compter
        $countQueryBuilder->select('COUNT(a.id)');

        return (int) $countQueryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * Get statistics for artists
     */
    public function getStatistics(): array
    {
        $currentYear = (int) date('Y');

        // Obtenir le nombre total d'artistes
        $totalArtists = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Obtenir le nombre d'artistes certifiés
        $certifiedArtists = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.isCertified = true')
            ->getQuery()
            ->getSingleScalarResult();

        // Calculer le pourcentage d'artistes certifiés
        $certifiedPercentage = $totalArtists > 0 ? ($certifiedArtists / $totalArtists) * 100 : 0;

        // Obtenir les inscriptions par mois pour l'année en cours
        $monthlyRegistrations = $this->createQueryBuilder('a')
            ->select("DATE_FORMAT(a.createdAt, '%Y-%m') as month, COUNT(a.id) as count")
            ->where("YEAR(a.createdAt) = :year")
            ->setParameter('year', $currentYear)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();

        // Organiser les données mensuelles
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthKey = sprintf('%04d-%02d', $currentYear, $i);
            $monthName = date('F', mktime(0, 0, 0, $i, 1));
            $monthlyData[$monthName] = 0;

            foreach ($monthlyRegistrations as $registration) {
                if ($registration['month'] === $monthKey) {
                    $monthlyData[$monthName] = (int) $registration['count'];
                    break;
                }
            }
        }

        // Total des inscriptions pour l'année en cours
        $yearlyRegistrations = array_sum($monthlyData);

        // Techniques les plus populaires
        $topTechniques = $this->createQueryBuilder('a')
            ->select('a.ecoTechnique as technique, COUNT(a.id) as count')
            ->groupBy('a.ecoTechnique')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();

        // Nombre total de journaux et tutoriels
        $totalJournals = $this->createQueryBuilder('a')
            ->select('SUM(SIZE(a.creationJournals)) as total')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        $totalTutorials = $this->createQueryBuilder('a')
            ->select('SUM(SIZE(a.tutorials)) as total')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        // Moyennes par artiste
        $avgJournalsPerArtist = $totalArtists > 0 ? $totalJournals / $totalArtists : 0;
        $avgTutorialsPerArtist = $totalArtists > 0 ? $totalTutorials / $totalArtists : 0;

        // Artistes les plus actifs (basé sur le contenu créé)
        $mostActiveArtists = $this->createQueryBuilder('a')
            ->select('a as artist')
            ->addSelect('SIZE(a.creationJournals) as journalCount')
            ->addSelect('SIZE(a.tutorials) as tutorialCount')
            ->addSelect('(SIZE(a.creationJournals) + SIZE(a.tutorials)) as totalContent')
            ->orderBy('totalContent', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return [
            'total_artists' => (int) $totalArtists,
            'certified_artists' => (int) $certifiedArtists,
            'certified_percentage' => $certifiedPercentage,
            'monthly_registrations' => $monthlyData,
            'current_year' => $currentYear,
            'yearly_registrations' => $yearlyRegistrations,
            'top_techniques' => $topTechniques,
            'total_journals' => (int) $totalJournals,
            'total_tutorials' => (int) $totalTutorials,
            'avg_journals_per_artist' => $avgJournalsPerArtist,
            'avg_tutorials_per_artist' => $avgTutorialsPerArtist,
            'most_active_artists' => array_map(function($result) {
                $artist = $result['artist'];
                return [
                    'name' => $artist->getName(),
                    'isCertified' => $artist->isIsCertified(),
                    'ecoTechnique' => $artist->getEcoTechnique(),
                    'createdAt' => $artist->getCreatedAt(),
                    'user' => $artist->getUser(),
                    'journalCount' => $result['journalCount'],
                    'tutorialCount' => $result['tutorialCount'],
                    'totalContent' => $result['totalContent']
                ];
            }, $mostActiveArtists),
        ];
    }
}