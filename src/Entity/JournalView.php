<?php

namespace App\Entity;

use App\Repository\JournalViewRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JournalViewRepository::class)]
#[ORM\UniqueConstraint(columns: ['user_id', 'journal_id'])]
class JournalView
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: CreationJournal::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?CreationJournal $journal = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $viewedAt = null;

    public function __construct()
    {
        $this->viewedAt = new \DateTimeImmutable();
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

    public function getJournal(): ?CreationJournal
    {
        return $this->journal;
    }

    public function setJournal(?CreationJournal $journal): static
    {
        $this->journal = $journal;
        return $this;
    }

    public function getViewedAt(): ?\DateTimeImmutable
    {
        return $this->viewedAt;
    }
}