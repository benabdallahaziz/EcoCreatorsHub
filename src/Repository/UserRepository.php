<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Pagine une requête
     */
    public function paginate($queryBuilder, int $page = 1, int $limit = 15): Paginator
    {
        $paginator = new Paginator($queryBuilder, true);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }

    /**
     * Trouve les utilisateurs par rôle
     */
    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('JSON_CONTAINS(u.roles, :role) = 1')
            ->setParameter('role', json_encode($role))
            ->orderBy('u.username', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des utilisateurs par terme
     */
    public function search(string $term): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email LIKE :term OR u.username LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les utilisateurs récents
     */
    public function findRecent(int $limit = 10): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les utilisateurs inactifs depuis une date donnée
     */
    public function findInactiveUsers(\DateTimeInterface $since): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('(u.updatedAt < :since OR u.updatedAt IS NULL)')
            ->andWhere('u.isActive = :active')
            ->setParameter('since', $since)
            ->setParameter('active', true)
            ->orderBy('u.updatedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Sauvegarde un utilisateur
     */
    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime un utilisateur
     */
    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Obtient les statistiques des utilisateurs
     */
    public function getStatistics(): array
    {
        $result = $this->createQueryBuilder('u')
            ->select([
                'COUNT(u.id) as total',
                'SUM(CASE WHEN u.isActive = true THEN 1 ELSE 0 END) as active',
                'SUM(CASE WHEN u.isVerified = true THEN 1 ELSE 0 END) as verified',
                'SUM(CASE WHEN JSON_CONTAINS(u.roles, :admin) = 1 THEN 1 ELSE 0 END) as admins',
                'SUM(CASE WHEN JSON_CONTAINS(u.roles, :artist) = 1 THEN 1 ELSE 0 END) as artists',
            ])
            ->setParameter('admin', json_encode('ROLE_ADMIN'))
            ->setParameter('artist', json_encode('ROLE_ARTIST'))
            ->getQuery()
            ->getSingleResult();

        // Assure que tous les champs sont présents même si null
        return [
            'total' => (int) ($result['total'] ?? 0),
            'active' => (int) ($result['active'] ?? 0),
            'verified' => (int) ($result['verified'] ?? 0),
            'admins' => (int) ($result['admins'] ?? 0),
            'artists' => (int) ($result['artists'] ?? 0),
        ];
    }

    /**
     * Trouve les utilisateurs sans artiste associé
     */
    public function findUsersWithoutArtist(): array
    {
        $subQuery = $this->getEntityManager()->createQueryBuilder()
            ->select('IDENTITY(a.user)')
            ->from('App\Entity\Artist', 'a')
            ->where('a.user IS NOT NULL')
            ->getQuery()
            ->getArrayResult();

        $userIdsWithArtist = array_column($subQuery, 1); // Récupère la deuxième colonne

        if (empty($userIdsWithArtist)) {
            return $this->createQueryBuilder('u')
                ->orderBy('u.username', 'ASC')
                ->getQuery()
                ->getResult();
        }

        return $this->createQueryBuilder('u')
            ->where('u.id NOT IN (:artistUserIds)')
            ->setParameter('artistUserIds', $userIdsWithArtist)
            ->orderBy('u.username', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les utilisateurs disponibles pour l'édition (sans artiste, sauf l'utilisateur courant)
     */
    public function findAvailableUsersForEdit(?User $currentUser = null): array
    {
        $usersWithoutArtist = $this->findUsersWithoutArtist();

        if ($currentUser && !in_array($currentUser, $usersWithoutArtist, true)) {
            $usersWithoutArtist[] = $currentUser;
        }

        return $usersWithoutArtist;
    }

    /**
     * Trouve un utilisateur par email (insensible à la casse)
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('LOWER(u.email) = LOWER(:email)')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve un utilisateur par nom d'utilisateur (insensible à la casse)
     */
    public function findOneByUsername(string $username): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('LOWER(u.username) = LOWER(:username)')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve les utilisateurs avec pagination et filtres
     */
    public function findWithFilters(array $filters = [], int $page = 1, int $limit = 15): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('u');

        if (!empty($filters['role'])) {
            $queryBuilder
                ->andWhere('JSON_CONTAINS(u.roles, :role) = 1')
                ->setParameter('role', json_encode($filters['role']));
        }

        if (isset($filters['isActive']) && $filters['isActive'] !== '') {
            $queryBuilder
                ->andWhere('u.isActive = :isActive')
                ->setParameter('isActive', (bool) $filters['isActive']);
        }

        if (isset($filters['isVerified']) && $filters['isVerified'] !== '') {
            $queryBuilder
                ->andWhere('u.isVerified = :isVerified')
                ->setParameter('isVerified', (bool) $filters['isVerified']);
        }

        if (!empty($filters['search'])) {
            $queryBuilder
                ->andWhere('u.email LIKE :search OR u.username LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        // Tri par défaut
        $orderBy = $filters['orderBy'] ?? 'u.createdAt';
        $orderDirection = $filters['orderDirection'] ?? 'DESC';
        $queryBuilder->orderBy($orderBy, $orderDirection);

        return $this->paginate($queryBuilder, $page, $limit);
    }

    /**
     * Compte le nombre total d'utilisateurs
     */
    public function countUsers(): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve les utilisateurs qui ont un rôle spécifique mais pas un autre
     */
    public function findByRoleExcluding(string $role, string $excludingRole): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('JSON_CONTAINS(u.roles, :role) = 1')
            ->andWhere('JSON_CONTAINS(u.roles, :excludingRole) = 0')
            ->setParameter('role', json_encode($role))
            ->setParameter('excludingRole', json_encode($excludingRole))
            ->orderBy('u.username', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les utilisateurs créés entre deux dates
     */
    public function findCreatedBetween(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}