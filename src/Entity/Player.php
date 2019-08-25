<?php

namespace App\Entity;

use App\Uuid\UuidableInterface;
use App\Uuid\UuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

class Player implements UuidableInterface, UserInterface
{
    use UuidTrait;

    private $name;

    private $id;

    private $is_registered;

    private $GamePlayerMetas;

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

	public function setIsRegistered(bool $is_registered): self
	{
		$this->is_registered = $is_registered;

		return $this;
	}

	public function getRoles()
	{
		return [];
	}

	public function getPassword()
	{
		return null;
	}

	public function getSalt()
	{
		return null;
	}

	public function getUsername()
	{
		return $this->name;
	}

	public function eraseCredentials()
	{

	}
}
