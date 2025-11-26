<?php
// src/Entity/Material.php

namespace App\Entity;

use App\Repository\MaterialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialRepository::class)]
class Material
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(type: 'integer')]
    private ?int $ecoScore = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isEcoFriendly = true;

    #[ORM\ManyToOne(inversedBy: 'materials')]
    private ?MaterialSupplier $supplier = null;

    #[ORM\ManyToMany(targetEntity: Tutorial::class, mappedBy: 'materials')]
    private Collection $tutorials;

    public function __construct()
    {
        $this->tutorials = new ArrayCollection();
    }

    // Getters et setters...
}