<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.', groups: ['registration', 'update'])]
#[UniqueEntity(fields: ['username'], message: 'Ce nom d\'utilisateur est déjà utilisé.', groups: ['registration', 'update'])]
#[Assert\Callback('validatePasswordConfirmation', groups: ['registration', 'password_change'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire.', groups: ['registration', 'update'])]
    #[Assert\Email(message: 'L\'email "{{ value }}" n\'est pas valide.', mode: 'strict', groups: ['registration', 'update'])]
    #[Assert\Length(
        min: 5,
        max: 180,
        minMessage: 'L\'email doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'L\'email ne peut pas dépasser {{ limit }} caractères.',
        groups: ['registration', 'update']
    )]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    #[Assert\All([
        new Assert\Choice([
            'choices' => ['ROLE_USER', 'ROLE_ARTIST', 'ROLE_ADMIN'],
            'message' => 'Le rôle "{{ value }}" n\'est pas valide.'
        ])
    ], groups: ['registration', 'update'])]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank(message: 'Le nom d\'utilisateur est obligatoire.', groups: ['registration', 'update'])]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le nom d\'utilisateur doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom d\'utilisateur ne peut pas dépasser {{ limit }} caractères.',
        groups: ['registration', 'update']
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_\-]+$/',
        message: 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres, tirets et underscores.',
        groups: ['registration', 'update']
    )]
    private ?string $username = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'is_verified', type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(name: 'verified_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $verifiedAt = null;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    // Champs non persistés pour le mot de passe
    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire.', groups: ['registration'])]
    #[Assert\Length(
        min: 12,
        max: 255,
        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.',
        groups: ['registration', 'password_change']
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
        message: 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&).',
        groups: ['registration', 'password_change']
    )]
    private ?string $plainPassword = null;

    #[Assert\NotBlank(message: 'La confirmation du mot de passe est obligatoire.', groups: ['registration'])]
    private ?string $confirmPassword = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->roles = ['ROLE_USER'];
        $this->isVerified = false;
        $this->isActive = true;
    }

    // Callback de validation
    public function validatePasswordConfirmation(ExecutionContextInterface $context): void
    {
        if (!empty($this->plainPassword)) {
            if (empty($this->confirmPassword)) {
                $context->buildViolation('La confirmation du mot de passe est requise.')
                    ->atPath('confirmPassword')
                    ->addViolation();
            } elseif ($this->plainPassword !== $this->confirmPassword) {
                $context->buildViolation('Les mots de passe ne correspondent pas.')
                    ->atPath('confirmPassword')
                    ->addViolation();
            }
        } elseif (!empty($this->confirmPassword)) {
            $context->buildViolation('Vous devez d\'abord entrer un nouveau mot de passe.')
                ->atPath('plainPassword')
                ->addViolation();
        }
    }

    // Getters et setters de base
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
        $this->email = trim(strtolower($email));
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $roles = array_unique($roles);
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }
        $this->roles = $roles;
        return $this;
    }

    public function addRole(string $role): static
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    public function removeRole(string $role): static
    {
        if (($key = array_search($role, $this->roles, true)) !== false) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }

    /**
     * Vérifie si l'utilisateur est administrateur
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('ROLE_ADMIN');
    }

    /**
     * Vérifie si l'utilisateur est artiste
     */
    public function isArtist(): bool
    {
        return $this->hasRole('ROLE_ARTIST');
    }

    /**
     * Vérifie si l'utilisateur est banni
     */
    public function isBanned(): bool
    {
        return $this->hasRole('ROLE_BANNED');
    }

    public function getPassword(): string
    {
        return $this->password ?? '';
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(?string $confirmPassword): static
    {
        $this->confirmPassword = $confirmPassword;
        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
        $this->confirmPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = trim($username);
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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        if ($isVerified && $this->verifiedAt === null) {
            $this->verifiedAt = new \DateTime();
        } elseif (!$isVerified) {
            $this->verifiedAt = null;
        }
        return $this;
    }

    public function getVerifiedAt(): ?\DateTimeInterface
    {
        return $this->verifiedAt;
    }

    public function setVerifiedAt(?\DateTimeInterface $verifiedAt): static
    {
        $this->verifiedAt = $verifiedAt;
        if ($verifiedAt !== null) {
            $this->isVerified = true;
        }
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    // Méthodes utilitaires
    /**
     * Retourne les initiales de l'utilisateur
     */
    public function getInitials(): string
    {
        $initials = '';

        if ($this->username) {
            // Prendre la première lettre de chaque mot (jusqu'à 2 mots)
            $words = explode(' ', $this->username);
            $count = 0;
            foreach ($words as $word) {
                if (!empty($word)) {
                    $initials .= strtoupper($word[0]);
                    $count++;
                    if ($count >= 2) {
                        break;
                    }
                }
            }
        }

        // Si pas d'initials, utiliser email
        if (empty($initials) && $this->email) {
            $emailParts = explode('@', $this->email);
            $username = $emailParts[0];
            $initials = strtoupper(substr($username, 0, 2));
        }

        // Si toujours vide, retourner 'US'
        return $initials ?: 'US';
    }

    /**
     * Retourne le nombre d'années depuis l'inscription
     */
    public function getAccountAge(): int
    {
        if (!$this->createdAt) {
            return 0;
        }

        $now = new \DateTime();
        $interval = $this->createdAt->diff($now);

        return $interval->y;
    }

    /**
     * Active le compte utilisateur
     */
    public function activate(): static
    {
        $this->isActive = true;
        return $this;
    }

    /**
     * Désactive le compte utilisateur
     */
    public function deactivate(): static
    {
        $this->isActive = false;
        return $this;
    }

    /**
     * Vérifie si le compte est verrouillé
     */
    public function isAccountNonLocked(): bool
    {
        return $this->isActive;
    }

    /**
     * Vérifie si le compte est expiré
     */
    public function isAccountNonExpired(): bool
    {
        return true; // Par défaut, les comptes n'expirent pas
    }

    /**
     * Vérifie si les credentials sont expirés
     */
    public function isCredentialsNonExpired(): bool
    {
        return true; // Par défaut, les credentials n'expirent pas
    }

    /**
     * Vérifie si l'utilisateur est activé
     */
    public function isEnabled(): bool
    {
        return $this->isActive && $this->isVerified;
    }

    /**
     * Méthode pour savoir si l'utilisateur est nouveau
     */
    public function isNew(): bool
    {
        return $this->id === null;
    }

    /**
     * Vérifie si le mot de passe doit être changé
     */
    public function shouldPasswordBeChanged(): bool
    {
        return $this->plainPassword !== null && $this->plainPassword !== '';
    }

    /**
     * Récupère le nom d'affichage (username ou email)
     */
    public function getDisplayName(): string
    {
        return $this->username ?? $this->email ?? 'Utilisateur';
    }

    /**
     * Vérifie si l'utilisateur peut être supprimé
     */
    public function canBeDeleted(): bool
    {
        // Par exemple, empêcher la suppression de l'administrateur principal
        // ou des utilisateurs avec des relations importantes
        return true;
    }

    // Lifecycle callbacks
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTime();
        }
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function __toString(): string
    {
        return $this->username ?? $this->email ?? 'Utilisateur #' . $this->id;
    }

    public function getSalt(): ?string
    {
        return null; // bcrypt gère son propre salt
    }

   #[ORM\OneToOne(mappedBy: 'user', targetEntity: Artist::class)]
    private ?Artist $artist = null;

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): self
    {
        $this->artist = $artist;
        return $this;
    }

}