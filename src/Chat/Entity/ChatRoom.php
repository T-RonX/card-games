<?php

namespace App\Chat\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Player;
use Doctrine\ORM\PersistentCollection;

class ChatRoom
{
	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $reference;

	/**
	 * @var ChatMessage[]|ArrayCollection
	 */
	private $ChatMessages;

	/**
	 * @var ArrayCollection|ChatRoomPlayer[]
	 */
	private $ChatRoomPlayers;

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
	 * @return ChatMessage[]|ArrayCollection
	 */
	public function getChatMessages(): iterable
	{
		return $this->ChatMessages;
	}

	public function addChatRoomPlayer(ChatRoomPlayer $chat_room_player)
	{
		$chat_room_player->setChatRoom($this);
		$this->ChatRoomPlayers->add($chat_room_player);
	}

	/**
	 * @return PersistentCollection|ChatRoomPlayer[]
	 */
	public function getChatRoomPlayers(): iterable
	{
		return $this->ChatRoomPlayers;
	}
}