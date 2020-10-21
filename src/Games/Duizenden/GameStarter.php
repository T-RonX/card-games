<?php

declare(strict_types=1);

namespace App\Games\Duizenden;

use App\Entity\Player;
use App\Game\GameFactory;
use App\Games\Duizenden\Event\GameEvent;
use App\Games\Duizenden\Event\GameEventType;
use App\Games\Duizenden\Initializer\Exception\InvalidDealerPlayerException;
use App\Games\Duizenden\Player\Exception\EmptyPlayerSetException;
use App\Games\Duizenden\Player\PlayerFactory;
use App\Lobby\Entity\Invitation;
use App\Lobby\Inviter;
use App\Player\PlayerType;
use App\Shufflers\ShufflerType;
use App\User\Player\PlayerFactory as GlobalPlayerFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GameStarter
{
	private GameFactory $game_factory;
	private PlayerFactory $game_player_factory;
	private GlobalPlayerFactory $global_player_factory;
	private Inviter $inviter;
	private EventDispatcherInterface $event_dispatcher;

	public function __construct(
		GameFactory $game_factory,
		PlayerFactory $game_player_factory,
		GlobalPlayerFactory $global_player_factory,
		Inviter $inviter,
		EventDispatcherInterface $event_dispatcher
	)
	{
		$this->game_factory = $game_factory;
		$this->game_player_factory = $game_player_factory;
		$this->global_player_factory = $global_player_factory;
		$this->inviter = $inviter;
		$this->event_dispatcher = $event_dispatcher;
	}

	/**
	 * @throws EmptyPlayerSetException
	 * @throws InvalidDealerPlayerException
	 */
	public function start(
		array $players,
		int $num_ai_players,
		bool $initial_shuffle,
		ShufflerType $initial_shuffle_algorithm,
		Player $first_dealer,
		bool $is_dealer_random,
		int $target_score,
		int $first_meld_minimum_points,
		int $round_finish_extra_points,
		bool $allow_first_turn_round_end,
		?Invitation $invitation
	): Game
	{
		$game_players = [];

		for ($i = 1; $i <= $num_ai_players; ++$i)
		{
			$players[] = $this->global_player_factory->create(sprintf('AI Player #%d', $i), PlayerType::AI());
		}

		foreach ($players as $player)
		{
			$game_players[] = $this->game_player_factory->create($player->getUuid());
		}

		$configurator = (new Configurator())
			->setPlayers($game_players)
			->setDoInitialShuffle($initial_shuffle)
			->setInitialShuffleAlgorithm($initial_shuffle ? $initial_shuffle_algorithm : null)
			->setFirstDealer($this->game_player_factory->create($first_dealer->getUuid()))
			->setIsDealerRandom($is_dealer_random)
			->setTargetScore($target_score)
			->setFirstMeldMinimumPoints($first_meld_minimum_points)
			->setRoundFinishExtraPoints($round_finish_extra_points)
			->setAllowFirstTurnRoundEnd($allow_first_turn_round_end);

		$game = $this->create($configurator);

		if ($invitation)
		{
			$this->inviter->assignGame($invitation, $game->getId());
		}

		$this->event_dispatcher->dispatch(new GameEvent($game), GameEventType::GAME_STARTED()->getValue());

		return $game;
	}

	/**
	 * @throws EmptyPlayerSetException
	 * @throws InvalidDealerPlayerException
	 */
	private function create(Configurator $configurator): Game
	{
		/** @var Game $game */
		$game = $this->game_factory->create(Game::NAME);
		$game->setCreatedMarking();
		$game->configure($configurator);

		return $game;
	}
}