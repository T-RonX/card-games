<?php

declare(strict_types=1);

namespace App\Lobby\Entity;

use App\Entity\Player;
use App\Games\Duizenden\Entity\GameMeta;
use App\Uuid\UuidableInterface;
use App\Uuid\UuidTrait;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Invitation implements UuidableInterface
{
	use UuidTrait;

	private ?int $id = null;

	private DateTimeInterface $created_at;

	/**
	 * @var Invitee[]|Collection
	 */
	private ?Collection $Invitees = null;

	private Invitee $Inviter;

	private ?string $game_id = null;

	public function __construct()
	{
		$this->Invitees = new ArrayCollection();
	}

	public function addPlayerInvite(Invitee $player_invite): self
	{
		$player_invite->setInvitation($this);

		$this->Invitees->add($player_invite);

		return $this;
	}

	public function setCreatedAt(DateTimeInterface $created_at): self
	{
		$this->created_at = $created_at;

		return $this;
	}

	public function setInviter(Invitee $Inviter): self
	{
		$this->Inviter = $Inviter;

		return $this;
	}

	/**
	 * @return Invitee[]
	 */
	public function getInvitees(): iterable
	{
		return $this->Invitees;
	}

	/**
	 * @return Player[]
	 */
	public function getAllPlayers(?bool $accepted = null): array
	{
		$players = [];

		foreach ($this->Invitees as $invitee)
		{
			if (true === $accepted && true === $invitee->isAccepted())
			{
				$players[] = $invitee->getPlayer();

			}
			elseif (false === $accepted && false === $invitee->isAccepted())
			{
				$players[] = $invitee->getPlayer();
			}
			elseif (null === $accepted)
			{
				$players[] = $invitee->getPlayer();
			}
		}

		return $players;
	}

	public function getInviteeByPlayer(Player $player): ?Invitee
	{
		foreach ($this->Invitees as $invitee)
		{
			if ($invitee->getPlayer()->getUuid() === $player->getUuid())
			{
				return $invitee;
			}
		}

		return null;
	}

	public function allInviteesAccepted(): bool
	{
		foreach ($this->Invitees as $invitee)
		{
			if (!$invitee->isAccepted())
			{
				return false;
			}
		}

		return true;
	}

	public function allInviteesResponded(): bool
	{
		foreach ($this->Invitees as $invitee)
		{
			if (null === $invitee->isAccepted())
			{
				return false;
			}
		}

		return true;
	}

	public function getUuid(): string
	{
		return $this->uuid;
	}

	public function getInviter(): Invitee
	{
		return $this->Inviter;
	}

	public function getCreatedAt(): DateTimeInterface
	{
		return $this->created_at;
	}

	public function setGameId(?string $game_id): self
	{
		$this->game_id = $game_id;

		return $this;
	}

	public function getGameId(): ?string
	{
		return $this->game_id;
	}

	public function hasGameId(): bool
	{
		return null !== $this->game_id;
	}
}