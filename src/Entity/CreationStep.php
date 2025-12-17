<?php
// src/Entity/CreationStep.php

namespace App\Entity;

use App\Repository\CreationStepRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreationStepRepository::class)]
class CreationStep
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\Column(type: 'integer')]
    private ?int $stepOrder = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $images = [];

    #[ORM\ManyToOne(inversedBy: 'creationSteps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CreationJournal $creationJournal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getStepOrder(): ?int
    {
        return $this->stepOrder;
    }

    public function setStepOrder(int $stepOrder): self
    {
        $this->stepOrder = $stepOrder;
        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;
        return $this;
    }

    public function getCreationJournal(): ?CreationJournal
    {
        return $this->creationJournal;
    }

    public function setCreationJournal(?CreationJournal $creationJournal): self
    {
        $this->creationJournal = $creationJournal;
        return $this;
    }

   
    public function getStepNumber(): ?int
    {
        return $this->stepOrder;
    }

   
    public function setStepNumber(int $stepNumber): self
    {
        return $this->setStepOrder($stepNumber);
    }

 
    public function getJournal(): ?CreationJournal
    {
        return $this->creationJournal;
    }


    public function setJournal(?CreationJournal $journal): self
    {
        return $this->setCreationJournal($journal);
    }
}