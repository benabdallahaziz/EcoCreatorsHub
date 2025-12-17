<?php

namespace App\Entity;

use App\Repository\JournalCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JournalCategoryRepository::class)]
class JournalCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: CreationJournal::class)]
    private Collection $creationJournals;

    public function __construct()
    {
        $this->creationJournals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return Collection<int, CreationJournal>
     */
    public function getCreationJournals(): Collection
    {
        return $this->creationJournals;
    }

    public function addCreationJournal(CreationJournal $creationJournal): self
    {
        if (!$this->creationJournals->contains($creationJournal)) {
            $this->creationJournals->add($creationJournal);
            $creationJournal->setCategory($this);
        }

        return $this;
    }

    public function removeCreationJournal(CreationJournal $creationJournal): self
    {
        if ($this->creationJournals->removeElement($creationJournal)) {
            if ($creationJournal->getCategory() === $this) {
                $creationJournal->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}