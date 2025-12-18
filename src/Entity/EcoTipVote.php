<?php

namespace App\Entity;

use App\Repository\EcoTipVoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EcoTipVoteRepository::class)]
#[ORM\UniqueConstraint(name: 'user_ecotip_unique', columns: ['user_id', 'eco_tip_id'])]
class EcoTipVote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ecoTipVotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'ecoTipVotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EcoTip $ecoTip = null;

    #[ORM\Column]
    private ?bool $isUpvote = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $votedAt = null;

    public function __construct()
    {
        $this->votedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getEcoTip(): ?EcoTip
    {
        return $this->ecoTip;
    }

    public function setEcoTip(?EcoTip $ecoTip): static
    {
        $this->ecoTip = $ecoTip;
        return $this;
    }

    public function isUpvote(): ?bool
    {
        return $this->isUpvote;
    }

    public function setUpvote(bool $isUpvote): static
    {
        $this->isUpvote = $isUpvote;
        return $this;
    }

    public function getVotedAt(): ?\DateTimeImmutable
    {
        return $this->votedAt;
    }

    public function setVotedAt(\DateTimeImmutable $votedAt): static
    {
        $this->votedAt = $votedAt;
        return $this;
    }
}