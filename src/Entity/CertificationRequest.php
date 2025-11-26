<?php
// src/Entity/CertificationRequest.php

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

    #[ORM\Column(type: 'text')]
    private ?string $motivation = null;

    #[ORM\Column(type: 'json')]
    private array $portfolio = [];

    #[ORM\Column(length: 20)]
    private ?string $status = 'pending'; // 'pending', 'approved', 'rejected'

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'certificationRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Artist $artist = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters et setters...
}