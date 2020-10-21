<?php

declare(strict_types=1);

namespace App\Games\Duizenden;

use App\CardPool\CardPool;
use App\Common\Meld\Melds;
use App\Common\Meld\Meld;
use App\Game\GameFactory;
use App\Games\Duizenden\Initializer\DiscardedCardPool;
use App\Games\Duizenden\Player\Player;
use App\Games\Duizenden\Player\Players;

class GameCloner
{
	private GameFactory $game_factory;

	public function __construct(GameFactory $game_factory)
	{
		$this->game_factory = $game_factory;
	}

	public function cloneGame(Game $game): Game
	{
		$state = $this->cloneState($game->getState());

		$c = $this->createGame();
		$c->setId($game->getId());
		$c->setDeckRebuilder($game->getDeckRebuilder());
		$c->setState($state);

		return $c;
	}

	private function cloneState(State $state): State
	{
		$players = $this->clonePlayersCollection($state->getPlayers());
		$dealing_player = $players->getPlayerById($state->getDealingPlayer()->getId());
		$undrawn_pool = $this->cloneCardPool($state->getUndrawnPool());
		$discarded_pool = $this->cloneDiscardedCardPool($state->getDiscardedPool());

		$c = new State($players, $dealing_player, $undrawn_pool, $discarded_pool);
		$c->setTargetScore($state->getTargetScore());
		$c->setFirstMeldMinimumPoints($state->getFirstMeldMinimumPoints());
		$c->setRoundFinishExtraPoints($state->getRoundFinishExtraPoints());
		$c->setAllowFirstTurnRoundEnd($state->getAllowFirstTurnRoundEnd());
		$c->setRound($state->getRound());
		$c->setTurn($state->getTurn());

		return $c;
	}

	private function cloneDiscardedCardPool(DiscardedCardPool $discarded_card_pool): DiscardedCardPool
	{
		$c = new DiscardedCardPool($discarded_card_pool->getCards(), $discarded_card_pool->getPointer());
		$c->isFirstCard($discarded_card_pool->isFirstCard());

		return $c;
	}

	private function clonePlayersCollection(Players $players_collection): Players
	{
		$players = $this->clonePlayers($players_collection->getPlayers());

		$c = new Players();
		$c->setPointer($players_collection->getPointer());
		$c->setPlayers($players);

		return $c;
	}

	/**
	 * @param Player[] $players
	 * @return Player[]
	 */
	private function clonePlayers(array $players): array
	{
		$c = [];

		foreach ($players as $player)
		{
			$c[] = $this->clonePlayer($player);
		}

		return $c;
	}

	private function clonePlayer(Player $player): Player
	{
		$hand = $this->cloneCardPool($player->getHand());
		$melds = $this->cloneMeldsCollection($player->getMelds());

		$c = new Player();
		$c->setId($player->getId());
		$c->setType($player->getType());
		$c->setName($player->getName());
		$c->setHand($hand);
		$c->setMelds($melds);
		$c->setShuffler($player->getShuffler());

		return $c;
	}

	private function cloneCardPool(CardPool $card_pool): CardPool
	{
		return new CardPool($card_pool->getCards(), $card_pool->getPointer());
	}

	private function cloneMeldsCollection(Melds $melds_collection): Melds
	{
		$melds = $this->cloneMelds($melds_collection->getMelds());

		$c = new Melds();
		$c->setPointer($melds_collection->getPointer());
		$c->setMelds($melds);

		return $c;
	}

	/**
	 * @param Meld[] $melds
	 * @return Meld[]
	 */
	private function cloneMelds(array $melds): array
	{
		$c = [];

		foreach ($melds as $meld)
		{
			$c[] = $this->cloneMeld($meld);
		}

		return $c;
	}
	private function cloneMeld(Meld $meld): Meld
	{
		return new Meld($this->cloneCardPool($meld->getCards()), $meld->getType());
	}

	private function createGame(): Game
	{
		$game = $this->game_factory->create(Game::NAME);

		if (!$game instanceof Game)
		{
			throw new \RuntimeException("Wrong game instance created during clone.");
		}

		return $game;
	}
}