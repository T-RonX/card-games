<?php

declare(strict_types=1);

namespace App\Chat;

use App\Chat\Entity\ChatRoom as ChatRoomEntity;
use App\Chat\Repository\ChatRoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\PublisherInterface;
use DateInterval;

class ChatRoomFactory
{
	private ChatRoomRepository $repository;

	private EntityManagerInterface $entity_manager;

	private PublisherInterface $publisher;

	public function __construct(
		ChatRoomRepository $repository,
		EntityManagerInterface $entity_manager,
		PublisherInterface $publisher
	)
	{
		$this->repository = $repository;
		$this->entity_manager = $entity_manager;
		$this->publisher = $publisher;
	}

	public function create(string $id): ChatRoom
	{
		$chat_room_entity = $this->repository->findByReference($id, new DateInterval('PT1H'));

		if (!$chat_room_entity)
		{
			$chat_room_entity = new ChatRoomEntity();
			$chat_room_entity->setReference($id);
			$this->entity_manager->persist($chat_room_entity);
			$this->entity_manager->flush();
		}

		return new ChatRoom($this->entity_manager, $chat_room_entity, $this->publisher);
	}
}