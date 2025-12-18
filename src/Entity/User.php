<?php
// src/Entity/User.php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $username = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Like::class)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'follower', targetEntity: Subscription::class)]
    private Collection $following;

    #[ORM\OneToMany(mappedBy: 'followed', targetEntity: Subscription::class)]
    private Collection $followers;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Artist $artist = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: EcoTip::class)]
    private Collection $ecoTips;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: EcoTipVote::class)]
    private Collection $ecoTipVotes;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->ecoTips = new ArrayCollection();
        $this->ecoTipVotes = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->roles = ['ROLE_USER'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

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

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Subscription>
     */
    public function getFollowing(): Collection
    {
        return $this->following;
    }

    public function addFollowing(Subscription $following): static
    {
        if (!$this->following->contains($following)) {
            $this->following->add($following);
            $following->setFollower($this);
        }

        return $this;
    }

    public function removeFollowing(Subscription $following): static
    {
        if ($this->following->removeElement($following)) {
            // set the owning side to null (unless already changed)
            if ($following->getFollower() === $this) {
                $following->setFollower(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Subscription>
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(Subscription $follower): static
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
            $follower->setFollowed($this);
        }

        return $this;
    }

    public function removeFollower(Subscription $follower): static
    {
        if ($this->followers->removeElement($follower)) {
            // set the owning side to null (unless already changed)
            if ($follower->getFollowed() === $this) {
                $follower->setFollowed(null);
            }
        }

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): static
    {
        // unset the owning side of the relation if necessary
        if ($artist === null && $this->artist !== null) {
            $this->artist->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($artist !== null && $artist->getUser() !== $this) {
            $artist->setUser($this);
        }

        $this->artist = $artist;

        return $this;
    }

    /**
     * @return Collection<int, EcoTip>
     */
    public function getEcoTips(): Collection
    {
        return $this->ecoTips;
    }

    public function addEcoTip(EcoTip $ecoTip): static
    {
        if (!$this->ecoTips->contains($ecoTip)) {
            $this->ecoTips->add($ecoTip);
            $ecoTip->setAuthor($this);
        }
        return $this;
    }

    public function removeEcoTip(EcoTip $ecoTip): static
    {
        if ($this->ecoTips->removeElement($ecoTip)) {
            if ($ecoTip->getAuthor() === $this) {
                $ecoTip->setAuthor(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, EcoTipVote>
     */
    public function getEcoTipVotes(): Collection
    {
        return $this->ecoTipVotes;
    }

    public function addEcoTipVote(EcoTipVote $ecoTipVote): static
    {
        if (!$this->ecoTipVotes->contains($ecoTipVote)) {
            $this->ecoTipVotes->add($ecoTipVote);
            $ecoTipVote->setUser($this);
        }
        return $this;
    }

    public function removeEcoTipVote(EcoTipVote $ecoTipVote): static
    {
        if ($this->ecoTipVotes->removeElement($ecoTipVote)) {
            if ($ecoTipVote->getUser() === $this) {
                $ecoTipVote->setUser(null);
            }
        }
        return $this;
    }
}