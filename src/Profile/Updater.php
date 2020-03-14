<?php

declare(strict_types=1);

namespace App\Profile;

use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;

class Updater
{
    private EntityManagerInterface $entity_manager;

    public function __construct(EntityManagerInterface $entity_manager)
    {
        $this->entity_manager = $entity_manager;
    }

    public function updateProfile(Player $player, string $name): void
    {
        $player->setName($name);

        $this->entity_manager->persist($player);
        $this->entity_manager->flush();
    }
}