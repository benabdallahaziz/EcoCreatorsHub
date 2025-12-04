<?php

namespace App\Entity;

use App\Repository\CertificationRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertificationRequestRepository::class)]
class CertificationRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Description ou motivation de l'artiste
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $motivation = null;

    // Liens portfolio ou fichiers
    #[ORM\Column(type: 'json', nullable: true)]
    private array $portfolio = [];

    // Documents supplémentaires (CIN, preuves…)
    #[ORM\Column(type: 'json', nullable: true)]
    private array $documents = [];

    // Statut de la demande
    #[ORM\Column(length: 20)]
    private ?string $status = 'pending';
    // 'pending', 'approved', 'rejected'

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'certificationRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Artist $artist = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters et setters...
}
