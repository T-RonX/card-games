<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Game
{
	private ?int $id = null;

	private int $sequence;

	private string $workflow_marking;

	private DateTimeImmutable $created_at;

	private string $undrawn_pool;

	private string $discarded_pool;

    private ?Collection $GamePlayers = null;

    private Collection $GameStates;

    private ?Game $Game = null;

    private ?GameMeta $GameMeta = null;

    private ?GamePlayer $CurrentPlayer = null;

	private bool $is_first_card;

	private ?int $round = null;

	private ?int $turn = null;

    public function __construct()
    {
        $this->GamePlayers = new ArrayCollection();
        $this->GameStates = new ArrayCollection();
    }

    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    public function setSequence(int $sequence): self
    {
        $this->sequence = $sequence;

        return $this;
    }

    public function getWorkflowMarking(): ?string
    {
        return $this->workflow_marking;
    }

    public function setWorkflowMarking(string $workflow_marking): self
    {
        $this->workflow_marking = $workflow_marking;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUndrawnPool(): array
    {
        return json_decode($this->undrawn_pool);
    }

    public function setUndrawnPool(array $undrawn_pool): self
    {
        $this->undrawn_pool = json_encode($undrawn_pool);

        return $this;
    }

    public function getDiscardedPool(): array
    {
        return json_decode($this->discarded_pool);
    }

    public function setDiscardedPool(array $discarded_pool): self
    {
        $this->discarded_pool = json_encode($discarded_pool);

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|GamePlayer[]
     */
    public function getGamePlayers(): Collection
    {
        return $this->GamePlayers;
    }

    public function addGamePlayer(GamePlayer $gamePlayer): self
    {
        if (!$this->GamePlayers->contains($gamePlayer))
        {
            $this->GamePlayers[] = $gamePlayer;
            $gamePlayer->setGame($this);
        }

        return $this;
    }

    public function removeGamePlayer(GamePlayer $gamePlayer): self
    {
        if ($this->GamePlayers->contains($gamePlayer))
        {
            $this->GamePlayers->removeElement($gamePlayer);

            if ($gamePlayer->getGame() === $this)
            {
                $gamePlayer->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGameStates(): Collection
    {
        return $this->GameStates;
    }

    public function addGameState(Game $gameState): self
    {
        if (!$this->GameStates->contains($gameState))
        {
            $this->GameStates[] = $gameState;
            $gameState->setGame($this);
        }

        return $this;
    }

    public function removeGameState(Game $gameState): self
    {
        if ($this->GameStates->contains($gameState))
        {
            $this->GameStates->removeElement($gameState);

            if ($gameState->getGame() === $this)
            {
                $gameState->setGame(null);
            }
        }

        return $this;
    }

    public function getGame(): ?self
    {
        return $this->Game;
    }

    public function setGame(?self $Game): self
    {
        $this->Game = $Game;

        return $this;
    }

    public function getGameMeta(): ?GameMeta
    {
        return $this->GameMeta;
    }

    public function setGameMeta(?GameMeta $GameMeta): self
    {
        $this->GameMeta = $GameMeta;

        return $this;
    }

    public function getCurrentPlayer(): ?GamePlayer
    {
        return $this->CurrentPlayer;
    }

    public function setCurrentPlayer(?GamePlayer $CurrentPlayer): self
    {
        $this->CurrentPlayer = $CurrentPlayer;

        return $this;
    }

    public function getIsFirstCard(): ?bool
    {
        return $this->is_first_card;
    }

    public function setIsFirstCard(bool $is_first_card): self
    {
        $this->is_first_card = $is_first_card;

        return $this;
    }

	public function getRound(): ?int
	{
		return $this->round;
	}

	public function setRound(?int $round): self
	{
		$this->round = $round;

		return $this;
	}

    public function getTurn(): ?int
    {
        return $this->turn;
    }

    public function setTurn(?int $turn): void
    {
        $this->turn = $turn;
    }
}
