<?php
// src/Entity/Artist.php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
class Artist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text')]
    private ?string $bio = null;

    #[ORM\Column(length: 255)]
    private ?string $ecoTechnique = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePicture = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isCertified = false;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToOne(inversedBy: 'artist', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
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
}