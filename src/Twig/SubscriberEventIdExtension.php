<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Player;
use App\Games\Duizenden\Game;
use App\Mercure\SubscriberIdGenerator;
use App\User\User\UserProvider;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SubscriberEventIdExtension extends AbstractExtension
{
    private SubscriberIdGenerator $id_generator;
    private UserProvider $user_provider;

    public function __construct(
        SubscriberIdGenerator $id_generator,
        UserProvider $user_provider
    )
    {
        $this->id_generator = $id_generator;
        $this->user_provider = $user_provider;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('player_subscriber_event_id', [$this, 'generatePlayerSubscriberId']),
            new TwigFunction('game_subscriber_event_id', [$this, 'generateGameSubscriberId']),
        ];
    }

    public function generatePlayerSubscriberId(): string
    {
        $player = $this->user_provider->getPlayer();

        if (!$player instanceof Player)
        {
            return '';
        }

        return $this->id_generator->generate($player->getUuid());
    }

    public function generateGameSubscriberId(Game $game): string
    {
        return $this->id_generator->generate($game->getId());
    }
}