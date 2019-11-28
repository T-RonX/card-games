<?php

declare(strict_types=1);

namespace App\Chat\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ChatRoom
{
	private ?int $id = null;

	private string $reference;

	/**
	 * @var ChatMessage[]|Collection
	 */
	private ?Collection $ChatMessages = null;

	/**
	 * @var ChatRoomPlayer[]|Collection
	 */
	private ?Collection $ChatRoomPlayers = null;

	public function __construct()
	{
		$this->ChatMessages = new ArrayCollection();
		$this->ChatRoomPlayers = new ArrayCollection();
	}

	public function setReference(?string $reference): self
	{
		$this->reference = $reference;

		return $this;
	}

	/**
	 * @return ChatMessage[]|Collection
	 */
	public function getChatMessages(): iterable
	{
		return $this->ChatMessages;
	}

	public function addChatRoomPlayer(ChatRoomPlayer $chat_room_player): void
	{
		$chat_room_player->setChatRoom($this);
		$this->ChatRoomPlayers->add($chat_room_player);
	}

	/**
	 * @return ChatRoomPlayer[]|Collection
	 */
	public function getChatRoomPlayers(): iterable
	{
		return $this->ChatRoomPlayers;
	}
}