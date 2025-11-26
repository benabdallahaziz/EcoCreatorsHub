<?php
// src/Entity/MaterialSupplier.php

namespace App\Entity;

use App\Repository\MaterialSupplierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialSupplierRepository::class)]
class MaterialSupplier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $contactEmail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\OneToMany(mappedBy: 'supplier', targetEntity: Material::class)]
    private Collection $materials;

    public function __construct()
    {
        $this->materials = new ArrayCollection();
    }

    // Getters et setters...
}