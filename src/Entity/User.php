<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['comment:read'])] 
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null; 

    #[ORM\Column]
    private array $roles = []; 

    #[ORM\Column]
    private ?string $password = null; 

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(['comment:read'])] 
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['comment:read'])] 
    private ?string $profilePicture = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Chaque utilisateur a au moins ce rôle
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Si des données sensibles sont stockées temporairement, les effacer ici.
    }

    public function getUserIdentifier(): string
    {
        return $this->email; // Symfony utilise `getUserIdentifier()` au lieu de `getUsername()`
    }
}