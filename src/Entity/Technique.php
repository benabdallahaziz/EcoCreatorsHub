<?php

namespace App\Entity;

use App\Repository\TechniqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TechniqueRepository::class)]
class Technique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    private ?string $category = null;

    #[ORM\Column(length: 50)]
    private ?string $difficulty = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $images = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $materials = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $steps = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'technique', targetEntity: ProjectTechnique::class)]
    private Collection $projectTechniques;

    #[ORM\ManyToMany(targetEntity: EcoTip::class)]
    #[ORM\JoinTable(name: 'technique_eco_tip')]
    private Collection $relatedEcoTips;

    public function __construct()
    {
        $this->projectTechniques = new ArrayCollection();
        $this->relatedEcoTips = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): static
    {
        $this->difficulty = $difficulty;
        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): static
    {
        $this->images = $images;
        return $this;
    }

    public function getMaterials(): ?string
    {
        return $this->materials;
    }

    public function setMaterials(?string $materials): static
    {
        $this->materials = $materials;
        return $this;
    }

    public function getSteps(): ?string
    {
        return $this->steps;
    }

    public function setSteps(?string $steps): static
    {
        $this->steps = $steps;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getProjectTechniques(): Collection
    {
        return $this->projectTechniques;
    }

    public function addProjectTechnique(ProjectTechnique $projectTechnique): static
    {
        if (!$this->projectTechniques->contains($projectTechnique)) {
            $this->projectTechniques->add($projectTechnique);
            $projectTechnique->setTechnique($this);
        }
        return $this;
    }

    public function removeProjectTechnique(ProjectTechnique $projectTechnique): static
    {
        if ($this->projectTechniques->removeElement($projectTechnique)) {
            if ($projectTechnique->getTechnique() === $this) {
                $projectTechnique->setTechnique(null);
            }
        }
        return $this;
    }

    public function getRelatedEcoTips(): Collection
    {
        return $this->relatedEcoTips;
    }

    public function addRelatedEcoTip(EcoTip $ecoTip): static
    {
        if (!$this->relatedEcoTips->contains($ecoTip)) {
            $this->relatedEcoTips->add($ecoTip);
        }
        return $this;
    }

    public function removeRelatedEcoTip(EcoTip $ecoTip): static
    {
        $this->relatedEcoTips->removeElement($ecoTip);
        return $this;
    }
}