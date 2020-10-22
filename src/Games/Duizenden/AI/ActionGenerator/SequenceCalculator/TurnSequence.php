<?php

declare(strict_types=1);

namespace App\Games\Duizenden\AI\ActionGenerator\SequenceCalculator;

use App\AI\Minimax\Action\Action;
use App\AI\Minimax\Action\ActionSequence;
use App\AI\Minimax\Action\ActionSequenceFactory;
use App\AI\Minimax\Action\SequenceNotValidException;
use App\AI\Minimax\Context\ContextInterface;
use App\Games\Duizenden\Actions\ActionType;
use App\Games\Duizenden\Actions\DiscardCard\DiscardCard;
use App\Games\Duizenden\Actions\DrawCard\FromDiscardedPool;
use App\Games\Duizenden\Actions\DrawCard\FromUndrawnPool;
use App\Games\Duizenden\Actions\Meld\ExtendMeld;
use App\Games\Duizenden\Actions\Meld\MeldCards;
use App\Games\Duizenden\Actions\QueenOfSpadesTrait;
use App\Games\Duizenden\AI\ActionGenerator\SequenceCalculatorInterface;
use App\Games\Duizenden\AI\Context\Context;
use App\Games\Duizenden\StateBuilder\AllowedActions;
use Generator;

class TurnSequence implements SequenceCalculatorInterface
{
	use QueenOfSpadesTrait;

	private DiscardCard $discard_card;
	private FromDiscardedPool $from_discarded_pool;
	private FromUndrawnPool $from_undrawn_pool;
	private ExtendMeld $extend_meld;
	private MeldCards $meld_cards;
	private ActionSequenceFactory $action_sequence_factory;
	/**
	 * @var AllowedActions
	 */
	private AllowedActions $allowed_actions;

	public function __construct(
		ActionSequenceFactory $action_sequence_factory,
		AllowedActions $allowed_actions,
		DiscardCard $discard_card,
		FromDiscardedPool $from_discarded_pool,
		FromUndrawnPool $from_undrawn_pool,
		ExtendMeld $extend_meld,
		MeldCards $meld_cards
	)
	{
		$this->action_sequence_factory = $action_sequence_factory;
		$this->allowed_actions = $allowed_actions;
		$this->discard_card = $discard_card;
		$this->from_discarded_pool = $from_discarded_pool;
		$this->from_undrawn_pool = $from_undrawn_pool;
		$this->extend_meld = $extend_meld;
		$this->meld_cards = $meld_cards;
	}

	public function getActionSequences(Context $context): Generator
	{
		$state = $context->getGame()->getState();
		$state->getPlayers()->getCurrentPlayer();

		yield $this->createDrawFromUndrawnAndDiscardSequence($context);
	}

	private function createDrawFromUndrawnAndDiscardSequence(Context $context): ActionSequence
	{
		return $this->action_sequence_factory->create($context, [
			new Action(function (Context $context) {
				$drawn_card = $this->from_undrawn_pool->draw($context->getGame());
				$this->updateAllowedActions($context);

				if ($this->isCardQueenOfSpades($drawn_card))
				{
					throw new SequenceNotValidException(__FUNCTION__);
				}

				$this->discard_card->discard($context->getGame(), $drawn_card);
				$this->updateAllowedActions($context);

				return $context;
			}),
		]);
	}

	private function updateAllowedActions(Context $context): void
	{
		$context->setAllowedActions($this->allowed_actions->getAllowedActions($context->getGame()));
	}

	private function canCreateMelds(): bool
	{

	}

	public function supports(array $actions): bool
	{
		return count($actions) === 2 &&
			in_array(ActionType::DRAW_FROM_UNDRAWN(), $actions, true) &&
			in_array(ActionType::DRAW_FROM_DISCARDED(), $actions, true);
	}
}