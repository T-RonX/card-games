<?php

declare(strict_types=1);

namespace App\Entity;

use App\Uuid\UuidableInterface;
use App\Uuid\UuidTrait;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UuidableInterface, UserInterface
{
    use UuidTrait;

    private ?int $id = null;
    private string $username;
    private string $password;
    private string $salt;
    private Player $Player;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlayer(): Player
    {
        return $this->Player;
    }

    public function setPlayer(Player $Player): self
    {
        $this->Player = $Player;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }
}
