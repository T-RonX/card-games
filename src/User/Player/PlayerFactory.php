<?php

declare(strict_types=1);

namespace App\User\Player;

use App\Entity\Player;
use App\Player\PlayerType;
use Doctrine\ORM\EntityManagerInterface;

class PlayerFactory
{
    private EntityManagerInterface $entity_manager;

    public function __construct(EntityManagerInterface $entity_manager)
    {
        $this->entity_manager = $entity_manager;
    }

    public function create(string $name, PlayerType $type): Player
    {
        $player = $this->createPlayerEntity($name, $type);
        $this->savePlayer($player);

        return $player;
    }

    private function createPlayerEntity(string $name, PlayerType $type): Player
    {
        return (new Player())
            ->setName($name)
            ->setType($type);
    }

    private function savePlayer(Player $player): void
    {
        $this->entity_manager->persist($player);
        $this->entity_manager->flush();
    }
}