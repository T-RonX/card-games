<?php

declare(strict_types=1);

namespace App\Chat\Entity;

use App\Entity\Player;
use DateTimeInterface;

class ChatRoomPlayer
{
	private ?int $id = null;

	private ChatRoom $ChatRoom;

	private ?Player $Player = null;

	private DateTimeInterface $last_activity_at;

	public function setChatRoom(ChatRoom $ChatRoom): self
	{
		$this->ChatRoom = $ChatRoom;

		return $this;
	}

	public function setPlayer(Player $Player): self
	{
		$this->Player = $Player;

		return $this;
	}

	public function setLastActivityAt(DateTimeInterface $created_at): self
	{
		$this->last_activity_at = $created_at;

		return $this;
	}

	public function getPlayer(): Player
	{
		return $this->Player;
	}

	public function getLastActivityAt(): DateTimeInterface
	{
		return $this->last_activity_at;
	}
}