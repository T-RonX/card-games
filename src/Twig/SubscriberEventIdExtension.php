<?php

namespace App\Twig;

use App\Entity\Player;
use App\Games\Duizenden\Game;
use App\Mercure\SubscriberIdGenerator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SubscriberEventIdExtension extends AbstractExtension
{
	/**
	 * @var SubscriberIdGenerator
	 */
	private $id_generator;
	/**
	 * @var TokenStorageInterface
	 */
	private $token_storage;

	public function __construct(
		SubscriberIdGenerator $id_generator,
		TokenStorageInterface $token_storage
	)
	{
		$this->id_generator = $id_generator;
		$this->token_storage = $token_storage;
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
    	$token = $this->token_storage->getToken();
    	$player = $token ? $token->getUser() : null;

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