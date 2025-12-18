<?php

namespace App\Entity;

use App\Repository\EcoTipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EcoTipRepository::class)]
class EcoTip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 100)]
    private ?string $category = null;

    #[ORM\ManyToOne(inversedBy: 'ecoTips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column]
    private ?bool $isApproved = false;

    #[ORM\Column]
    private ?int $votes = 0;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $image = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'ecoTip', targetEntity: EcoTipVote::class)]
    private Collection $ecoTipVotes;

    public function __construct()
    {
        $this->ecoTipVotes = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;
        return $this;
    }

    public function isApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setApproved(bool $isApproved): static
    {
        $this->isApproved = $isApproved;
        return $this;
    }

    public function getVotes(): ?int
    {
        return $this->votes;
    }

    public function setVotes(int $votes): static
    {
        $this->votes = $votes;
        return $this;
    }

    public function getImage(): ?array
    {
        return $this->image;
    }

    public function setImage(?array $image): static
    {
        $this->image = $image;
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

    public function getEcoTipVotes(): Collection
    {
        return $this->ecoTipVotes;
    }

    public function addEcoTipVote(EcoTipVote $ecoTipVote): static
    {
        if (!$this->ecoTipVotes->contains($ecoTipVote)) {
            $this->ecoTipVotes->add($ecoTipVote);
            $ecoTipVote->setEcoTip($this);
        }
        return $this;
    }

    public function removeEcoTipVote(EcoTipVote $ecoTipVote): static
    {
        if ($this->ecoTipVotes->removeElement($ecoTipVote)) {
            if ($ecoTipVote->getEcoTip() === $this) {
                $ecoTipVote->setEcoTip(null);
            }
        }
        return $this;
    }
}