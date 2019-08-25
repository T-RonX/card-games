<?php

namespace App\Chat\Entity;

use App\Entity\Player;
use DateTimeInterface;

class ChatMessage
{
	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var ChatRoom
	 */
	private $ChatRoom;

	/**
	 * @var Player
	 */
	private $Player;

	/**
	 * @var DateTimeInterface
	 */
	private $created_at;

	/**
	 * @var string
	 */
	private $message;

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