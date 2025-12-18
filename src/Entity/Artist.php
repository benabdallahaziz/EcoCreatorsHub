<?php
// src/Entity/Artist.php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
#[UniqueEntity(fields: ['user'], message: 'Cet utilisateur a déjà un profil artiste.')]
class Artist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom de l\'artiste est obligatoire.')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le nom de l\'artiste doit faire au moins {{ limit }} caractères.',
        maxMessage: 'Le nom de l\'artiste ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-ZÀ-ÿ0-9\s\-\'\.]+$/u',
        message: 'Le nom de l\'artiste contient des caractères non autorisés.'
    )]
    private ?string $name = null;

    #[ORM\Column(type: 'text',nullable: true)]

    #[Assert\Length(
        min: 1,
        max: 2000,
        minMessage: 'La biographie doit faire au moins {{ limit }} caractères.',
        maxMessage: 'La biographie ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $bio = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La technique écologique est obligatoire.')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'La technique écologique doit faire au moins {{ limit }} caractères.',
        maxMessage: 'La technique écologique ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $ecoTechnique = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'L\'URL de la photo de profil n\'est pas valide.')]
    #[Assert\Length(max: 255, maxMessage: 'L\'URL ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $profilePicture = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isCertified = false;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    // ✅ Relation OneToOne vers User
    #[ORM\OneToOne(inversedBy: 'artist', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'L\'utilisateur associé est obligatoire.')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'artist', targetEntity: CreationJournal::class)]
    private Collection $creationJournals;

    #[ORM\OneToMany(mappedBy: 'artist', targetEntity: Tutorial::class)]
    private Collection $tutorials;

    #[ORM\OneToMany(mappedBy: 'artist', targetEntity: CertificationRequest::class)]
    private Collection $certificationRequests;

    public function __construct()
    {
        $this->creationJournals = new ArrayCollection();
        $this->tutorials = new ArrayCollection();
        $this->certificationRequests = new ArrayCollection();
        $this->createdAt = new \DateTime();
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

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(string $bio): static
    {
        $this->bio = $bio;
        return $this;
    }

    public function getEcoTechnique(): ?string
    {
        return $this->ecoTechnique;
    }

    public function setEcoTechnique(string $ecoTechnique): static
    {
        $this->ecoTechnique = $ecoTechnique;
        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    public function isIsCertified(): ?bool
    {
        return $this->isCertified;
    }

    public function setIsCertified(bool $isCertified): static
    {
        $this->isCertified = $isCertified;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    // ... les autres getters/setters pour les Collections restent inchangés
    // ... __toString(), getInitials(), getStatusBadge(), getStatusText(), getContentCount(), getRecentActivity()


    /**
     * @return Collection<int, CreationJournal>
     */
    public function getCreationJournals(): Collection
    {
        return $this->creationJournals;
    }

    public function addCreationJournal(CreationJournal $creationJournal): static
    {
        if (!$this->creationJournals->contains($creationJournal)) {
            $this->creationJournals->add($creationJournal);
            $creationJournal->setArtist($this);
        }

        return $this;
    }

    public function removeCreationJournal(CreationJournal $creationJournal): static
    {
        if ($this->creationJournals->removeElement($creationJournal)) {
            // set the owning side to null (unless already changed)
            if ($creationJournal->getArtist() === $this) {
                $creationJournal->setArtist(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tutorial>
     */
    public function getTutorials(): Collection
    {
        return $this->tutorials;
    }

    public function addTutorial(Tutorial $tutorial): static
    {
        if (!$this->tutorials->contains($tutorial)) {
            $this->tutorials->add($tutorial);
            $tutorial->setArtist($this);
        }

        return $this;
    }

    public function removeTutorial(Tutorial $tutorial): static
    {
        if ($this->tutorials->removeElement($tutorial)) {
            // set the owning side to null (unless already changed)
            if ($tutorial->getArtist() === $this) {
                $tutorial->setArtist(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CertificationRequest>
     */
    public function getCertificationRequests(): Collection
    {
        return $this->certificationRequests;
    }

    public function addCertificationRequest(CertificationRequest $certificationRequest): static
    {
        if (!$this->certificationRequests->contains($certificationRequest)) {
            $this->certificationRequests->add($certificationRequest);
            $certificationRequest->setArtist($this);
        }

        return $this;
    }

    public function removeCertificationRequest(CertificationRequest $certificationRequest): static
    {
        if ($this->certificationRequests->removeElement($certificationRequest)) {
            // set the owning side to null (unless already changed)
            if ($certificationRequest->getArtist() === $this) {
                $certificationRequest->setArtist(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? 'Artiste';
    }

    public function getInitials(): string
    {
        $initials = '';
        $words = explode(' ', $this->name);

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }

        return substr($initials, 0, 2);
    }

    public function getStatusBadge(): string
    {
        return $this->isCertified ? 'success' : 'warning';
    }

    public function getStatusText(): string
    {
        return $this->isCertified ? 'Certifié' : 'Non certifié';
    }

    public function getContentCount(): int
    {
        return $this->creationJournals->count() + $this->tutorials->count();
    }

    public function getRecentActivity(): ?\DateTimeInterface
    {
        $latestJournal = $this->creationJournals->reduce(
            fn($latest, $journal) => $journal->getDate() > $latest ? $journal->getDate() : $latest,
            $this->createdAt
        );

        $latestTutorial = $this->tutorials->reduce(
            fn($latest, $tutorial) => $tutorial->getCreatedAt() > $latest ? $tutorial->getCreatedAt() : $latest,
            $this->createdAt
        );

        return max($latestJournal, $latestTutorial);
    }
}