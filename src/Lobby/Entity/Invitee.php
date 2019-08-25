<?php

namespace App\Lobby\Entity;

use App\Entity\Player;

class Invitee
{
	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var bool|null
	 */
	private $accepted;

	/**
	 * @var Player
	 */
	private $Player;

	/**
	 * @var Invitation
	 */
	private $Invitation;

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