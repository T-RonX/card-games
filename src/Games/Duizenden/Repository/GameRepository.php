<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Repository;

use App\Entity\Player;
use App\Games\Duizenden\Entity\Game;
use App\Games\Duizenden\Workflow\MarkingType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

final class GameRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Game::class);
	}

	/**
	 * @throws NonUniqueResultException
	 */
	public function loadLastGameState(string $uuid): ?Game
	{
		return $this->getLatestGameQueryBuilder($uuid)
			->addSelect('gp, gpm, gm, dpm, p')
			->leftJoin('g.GameMeta', 'gm')
			->leftJoin('gm.DealingPlayerMeta', 'dpm')
			->leftJoin('g.GamePlayers', 'gp')
			->leftJoin('gp.GamePlayerMeta', 'gpm')
			->leftJoin('gpm.Player', 'p')
			->getQuery()
			->getOneOrNullResult();
	}

	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	public function getLatestGameStateMarking(string $uuid): ?string
	{
		return $this->getLatestGameQueryBuilder($uuid)
			->select('g.workflow_marking')
			->getQuery()
			->getSingleScalarResult();
	}

	/**
	 * @return Game[]
	 */
	public function getAllLatestGames(): array
	{
		return $this->getLatestGamesQueryBuilder()
			->getQuery()
			->execute();
	}

	/**
	 * @return Game[]
	 */
	public function getAllLatestGamesByPlayer(Player $player): array
	{
		return $this->getLatestGamesQueryBuilder('g')
			->join('g.GamePlayers', 'gps')
			->join('gps.GamePlayerMeta', 'gpm', Join::WITH, 'IDENTITY(gpm.Player) = :player_id')
			->setParameter('player_id', $player->getId())
			->getQuery()
			->execute();
	}

	/**
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function getRoundsPlayed(string $uuid): int
	{
		return (int)$this->createQueryBuilder('g')
			->select('MAX(g.round) as rounds')
			->join('g.GameMeta', 'gm', Join::WITH, 'gm.uuid = :uuid')
			->where('g.workflow_marking NOT IN (:markings)')
			->setParameter('uuid', $uuid)
			->setParameter('markings', MarkingType::CONFIGURED()->getValue())
			->getQuery()
			->getSingleScalarResult();
	}

	private function getLatestGameQueryBuilder(string $uuid, string $alias = 'g'): QueryBuilder
	{
		return $this->createQueryBuilder($alias)
			->join(sprintf('%s.GameMeta', $alias), sprintf('%s_meta', $alias), Join::WITH, sprintf('%s_meta.uuid = :uuid', $alias))
			->leftJoin(Game::class, sprintf('%1$s_latest', $alias), Join::WITH, sprintf('%1$s.Game = %1$s_latest.Game AND %1$s_latest.sequence > %1$s.sequence', $alias))
			->setParameter('uuid', $uuid)
			->where(sprintf('%s_latest.id IS NULL', $alias));
	}

	private function getLatestRoundQueryBuilder(string $uuid, string $alias = 'g'): QueryBuilder
	{
		return $this->createQueryBuilder($alias)
			->join(sprintf('%s.GameMeta', $alias), sprintf('%s_meta', $alias), Join::WITH, sprintf('%s_meta.uuid = :uuid', $alias))
			->leftJoin(Game::class, sprintf('%1$s_latest', $alias), Join::WITH, sprintf('%1$s.Game = %1$s_latest.Game AND %1$s_latest.round > %1$s.round AND %1$s_latest.round IS NOT NULL', $alias))
			->setParameter('uuid', $uuid)
			->where(sprintf('%1$s_latest.id IS NULL AND %1$s.round IS NOT NULL', $alias));
	}

	private function getLatestGamesQueryBuilder(string $alias = 'g'): QueryBuilder
	{
		return $this->createQueryBuilder($alias)
			->join(sprintf('%s.GameMeta', $alias), sprintf('%s_meta', $alias))
			->leftJoin(Game::class, sprintf('%1$s_latest', $alias), Join::WITH, sprintf('%1$s.Game = %1$s_latest.Game AND %1$s_latest.sequence > %1$s.sequence', $alias))
			->where(sprintf('%s_latest.id IS NULL', $alias));
	}

	public function getPlayersBySequences(string $game_id, array $sequences): array
	{
		return $this->createQueryBuilder('g', 'g.round')
			->select('g.round, p.uuid AS player_id')
			->join('g.GameMeta', 'gm', Join::WITH, 'gm.uuid = :uuid')
			->join('g.CurrentPlayer', 'cp')
			->join('cp.GamePlayerMeta', 'cpm')
			->join('cpm.Player', 'p')
			->where('g.sequence IN (:sequences)')
			->setParameter('uuid', $game_id)
			->setParameter('sequences', $sequences)
			->getQuery()
			->getResult(Query::HYDRATE_ARRAY);
	}
}