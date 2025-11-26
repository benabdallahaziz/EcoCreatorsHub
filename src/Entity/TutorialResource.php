<?php
// src/Entity/TutorialResource.php

namespace App\Entity;

use App\Repository\TutorialResourceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TutorialResourceRepository::class)]
class TutorialResource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $filePath = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null; // 'image', 'video', 'pdf'

    #[ORM\ManyToOne(inversedBy: 'resources')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tutorial $tutorial = null;

    // Getters et setters...
}