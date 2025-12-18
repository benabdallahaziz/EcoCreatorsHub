<?php

namespace App\Entity;

use App\Repository\ProjectTechniqueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectTechniqueRepository::class)]
class ProjectTechnique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'projectTechniques')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CreationJournal $project = null;

    #[ORM\ManyToOne(inversedBy: 'projectTechniques')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Technique $technique = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $addedAt = null;

    public function __construct()
    {
        $this->addedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?CreationJournal
    {
        return $this->project;
    }

    public function setProject(?CreationJournal $project): static
    {
        $this->project = $project;
        return $this;
    }

    public function getTechnique(): ?Technique
    {
        return $this->technique;
    }

    public function setTechnique(?Technique $technique): static
    {
        $this->technique = $technique;
        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeImmutable $addedAt): static
    {
        $this->addedAt = $addedAt;
        return $this;
    }
}