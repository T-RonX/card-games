<?php

declare(strict_types=1);

namespace App\Entity;

use App\Games\Duizenden\Entity\GamePlayerMeta;
use App\Uuid\UuidableInterface;
use App\Uuid\UuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

class Player implements UuidableInterface, UserInterface
{
    use UuidTrait;

    private ?int $id = null;
    private string $name;
    private ?User $User = null;

    /**
     * @var GamePlayerMeta[]|Collection
     */
    private Collection $GamePlayerMetas;

    public function __construct()
    {
        $this->GamePlayerMetas = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        return [];
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->name;
    }

    public function eraseCredentials(): void
    {

    }

    public function hasUser(): bool
    {
        return $this->User === null;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $user): self
    {
        $this->User = $user;

        return $this;
    }
}
