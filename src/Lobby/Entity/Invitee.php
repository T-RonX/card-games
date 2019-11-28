<?php

declare(strict_types=1);

namespace App\Lobby\Entity;

use App\Entity\Player;

class Invitee
{
	private ?int $id = null;

	private ?bool $accepted;

	private ?Player $Player = null;

	private Invitation $Invitation;

	public function setInvitation(Invitation $invite): self
	{
		$this->Invitation = $invite;

		return $this;
	}

	public function setPlayer(Player $Player): self
	{
		$this->Player = $Player;

		return $this;
	}

	public function getPlayer(): Player
	{
		return $this->Player;
	}

	public function setAccepted(?bool $accepted): self
	{
		$this->accepted = $accepted;

		return $this;
	}

	public function isAccepted(): ?bool
	{
		return $this->accepted;
	}
}