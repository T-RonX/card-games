<?php

declare(strict_types=1);

namespace App\Chat\Entity;

use App\Entity\Player;
use DateTimeInterface;

class ChatMessage
{
	private ?int $id = null;

	private ChatRoom $ChatRoom;

	private ?Player $Player = null;

	private DateTimeInterface $created_at;

	private string $message;

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

	public function setCreatedAt(DateTimeInterface $created_at): self
	{
		$this->created_at = $created_at;
		return $this;
	}

	public function setMessage(string $message): self
	{
		$this->message = $message;
		return $this;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	public function getPlayer(): Player
	{
		return $this->Player;
	}

	public function getCreatedAt(): DateTimeInterface
	{
		return $this->created_at;
	}
}