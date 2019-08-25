<?php

namespace App\Games\Duizenden\Entity;

use App\Uuid\UuidTrait;
use App\Uuid\UuidableInterface;

class GameMeta implements UuidableInterface
{
	use UuidTrait;

    private $id;

    private $DealingPlayerMeta;

    private $target_score;

    private $deck_rebuilder;

	private $first_meld_minimum_points;

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
}
