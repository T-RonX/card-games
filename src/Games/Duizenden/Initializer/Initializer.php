<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Initializer;

use App\CardPool\CardPool;
use App\Deck\DeckFactory;
use App\DeckRebuilder\DeckRebuilderFactory;
use App\DeckRebuilder\DeckRebuilderInterface;
use App\Decks\DeckType;
use App\Games\Duizenden\Configurator;
use App\Games\Duizenden\Initializer\Exception\InvalidDealerPlayerException;
use App\Games\Duizenden\Player\Exception\EmptyPlayerSetException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\State;
use App\Shuffler\ShufflerFactory;
use App\Shuffler\ShufflerInterface;

class Initializer
{
	private ShufflerFactory $shuffler_factory;

	private DeckFactory $deck_factory;

	private DeckRebuilderFactory $deck_rebuilder_factory;

	public function __construct(
		ShufflerFactory $shuffling_factory,
		DeckFactory $deck_factory,
		DeckRebuilderFactory $deck_rebuilder_factory
	)
	{
		$this->shuffler_factory = $shuffling_factory;
		$this->deck_factory = $deck_factory;
		$this->deck_rebuilder_factory = $deck_rebuilder_factory;
	}

	/**
	 * @throws EmptyPlayerSetException
	 * @throws InvalidDealerPlayerException
	 */
	public function createInitialState(Configurator $config): State
	{
		$shuffler = $this->getShufflerFromConfigurator($config);
		$pack = $this->createInitialPack($shuffler);
		$config->getPlayers()->resetCards();
		$dealing_player = $this->getDealingPlayerFromConfigurator($config);

		$state = new State($config->getPlayers(), $dealing_player, $pack, new DiscardedCardPool());
		$state->setTargetScore($config->getTargetScore());
		$state->setFirstMeldMinimumPoints($config->getFirstMeldMinimumPoints());
		$state->setRoundFinishExtraPoints($config->getRoundFinishExtraPoints());

		return $state;
	}

	/**
	 * @throws InvalidDealerPlayerException
	 * @throws EmptyPlayerSetException
	 */
	private function getDealingPlayerFromConfigurator(Configurator $config): PlayerInterface
	{
		if ($config->getIsDealerRandom())
		{
			return $config->getPlayers()->getRandomPlayer();
		}
		elseif ($config->hasFirstDealer() && !$config->getPlayers()->has($config->getFirstDealer()))
		{
			throw new InvalidDealerPlayerException("Invalid dealing player set.");
		}
		elseif ($config->hasFirstDealer())
		{
			return $config->getFirstDealer();
		}

		return $config->getPlayers()->getFirstPlayer();
	}

	private function getShufflerFromConfigurator(Configurator $config): ?ShufflerInterface
	{
		return $config->getDoInitialShuffle() ? $this->shuffler_factory->create($config->getInitialShuffleAlgorithm()) : null;
	}

	public function getDeckRebuilderFromConfig(Configurator $config): ?DeckRebuilderInterface
	{
		return $this->deck_rebuilder_factory->create($config->getDeckRebuilderAlgorithm());
	}

	private function createInitialPack(?ShufflerInterface $shuffler = null): CardPool
	{
		$deck1 = $this->deck_factory->create(DeckType::STANDARD108_BLUE());
		$deck2 = $this->deck_factory->create(DeckType::STANDARD108_RED());

		$pack = new CardPool();
		$pack->addCards($deck1->getCards());
		$pack->addCards($deck2->getCards());

		if ($shuffler)
		{
			$pack->shuffle($shuffler);
		}

		return $pack;
	}
}