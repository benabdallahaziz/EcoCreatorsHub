<?php
// src/Entity/CreationStep.php

namespace App\Entity;

use App\Repository\CreationStepRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreationStepRepository::class)]
class CreationStep
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\Column(type: 'integer')]
    private ?int $stepOrder = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $images = [];

    #[ORM\ManyToOne(inversedBy: 'creationSteps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CreationJournal $creationJournal = null;

    // Getters et setters...
}