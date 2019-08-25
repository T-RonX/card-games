<?php

namespace App\Games\Duizenden\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Game
{
    private $sequence;

    private $workflow_marking;

    private $created_at;

    private $undrawn_pool;

    private $discarded_pool;

    private $id;

    private $GamePlayers;

    private $GameStates;

    private $Game;

    private $GameMeta;

    private $CurrentPlayer;

	private $is_first_card;

	private $round;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUndrawnPool()
    {
        return $this->undrawn_pool;
    }

    public function setUndrawnPool($undrawn_pool): self
    {
        $this->undrawn_pool = $undrawn_pool;

        return $this;
    }

    public function getDiscardedPool()
    {
        return $this->discarded_pool;
    }

    public function setDiscardedPool($discarded_pool): self
    {
        $this->discarded_pool = $discarded_pool;

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
        if (!$this->GamePlayers->contains($gamePlayer)) {
            $this->GamePlayers[] = $gamePlayer;
            $gamePlayer->setGame($this);
        }

        return $this;
    }

    public function removeGamePlayer(GamePlayer $gamePlayer): self
    {
        if ($this->GamePlayers->contains($gamePlayer)) {
            $this->GamePlayers->removeElement($gamePlayer);
            // set the owning side to null (unless already changed)
            if ($gamePlayer->getGame() === $this) {
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
        if (!$this->GameStates->contains($gameState)) {
            $this->GameStates[] = $gameState;
            $gameState->setGame($this);
        }

        return $this;
    }

    public function removeGameState(Game $gameState): self
    {
        if ($this->GameStates->contains($gameState)) {
            $this->GameStates->removeElement($gameState);
            // set the owning side to null (unless already changed)
            if ($gameState->getGame() === $this) {
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
}
