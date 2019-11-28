<?php

declare(strict_types=1);

namespace App\Controller\Duizenden;

use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Game\GameFactory;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\GameLoader;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

trait LoadGameTrait
{
	protected GameFactory $game_factory;

	protected GameLoader $game_loader;

	protected SessionInterface $session;

	/**
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 * @throws InvalidCardIdException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 * @throws GameNotFoundException
	 */
	private function loadGame(?string $uuid = null): Game
	{
		if (null === $uuid)
		{
			$uuid = $this->session->get('game_id');
		}

		/** @var Game $game */
		$game = $this->game_factory->create(Game::NAME);
		$this->game_loader->load($game, $uuid);

		return $game;
	}

	/**
	 * @required
	 */
	public function setGameFactory(GameFactory $game_factory): void
	{
		$this->game_factory = $game_factory;
	}

	/**
	 * @required
	 */
	public function setGameLoader(GameLoader $game_loader): void
	{
		$this->game_loader = $game_loader;
	}

	/**
	 * @required
	 */
	public function setSession(SessionInterface $session): void
	{
		$this->session = $session;
	}
}