<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Entity;

use App\Uuid\UuidableInterface;
use App\Uuid\UuidTrait;

class GameMeta implements UuidableInterface
{
	use UuidTrait;

    private ?int $id = null;

    private ?GamePlayerMeta $DealingPlayerMeta = null;

    private ?int $target_score = null;

    private ?string $deck_rebuilder = null;

	private ?int $first_meld_minimum_points = null;

	private ?int $round_finish_extra_points = null;

	private ?bool $allow_first_turn_round_end = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDealingPlayerMeta(): ?GamePlayerMeta
    {
        return $this->DealingPlayerMeta;
    }

    public function setDealingPlayerMeta(?GamePlayerMeta $DealingPlayerMeta): self
    {
        $this->DealingPlayerMeta = $DealingPlayerMeta;

        return $this;
    }

    public function getTargetScore(): ?int
    {
        return $this->target_score;
    }

    public function setTargetScore(?int $target_score): self
    {
        $this->target_score = $target_score;

        return $this;
    }

	public function getDeckRebuilder(): ?string
	{
		return $this->deck_rebuilder;
	}

	public function setDeckRebuilder(?string $deck_rebuilder): self
	{
		$this->deck_rebuilder = $deck_rebuilder;

		return $this;
	}

	public function getFirstMeldMinimumPoints(): ?int
	{
		return $this->first_meld_minimum_points;
	}

	public function setFirstMeldMinimumPoints(?int $points): self
	{
		$this->first_meld_minimum_points = $points;

		return $this;
	}

	public function setRoundFinishExtraPoints(?int $round_finish_extra_points): self
	{
		$this->round_finish_extra_points = $round_finish_extra_points;

		return $this;
	}

	public function getRoundFinishExtraPoints(): int
	{
		return $this->round_finish_extra_points;
	}

    public function allowFirstTurnRoundEnd(): ?bool
    {
        return $this->allow_first_turn_round_end;
    }

    public function setAllowFirstTurnRoundEnd(?bool $allow): void
    {
        $this->allow_first_turn_round_end = $allow;
    }
}
