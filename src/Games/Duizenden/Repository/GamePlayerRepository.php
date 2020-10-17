<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Repository;

use App\Games\Duizenden\Entity\Game;
use App\Games\Duizenden\Entity\GamePlayer;
use App\Games\Duizenden\Workflow\MarkingType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;

final class GamePlayerRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, GamePlayer::class);
	}

	/**
	 * @return array
	 */
	public function getGameScoreData(string $game_id): array
	{
		return $this->createQueryBuilder('gp')
			->select('p.uuid AS player_uuid, gp.hand, gp.melds, g.sequence, g.round')
			->join('gp.Game', 'g', Join::WITH, 'g.workflow_marking in (:marking)')
			->join('g.GameMeta', 'gm', Join::WITH, 'gm.uuid = :uuid')
			->join('gp.GamePlayerMeta', 'gpm')
			->join('gpm.Player', 'p')
			->orderBy('g.created_at', 'ASC')
			->addOrderBy('gp.Game', 'ASC')
			->setParameter('marking', [MarkingType::ROUND_END()->getValue(), MarkingType::GAME_END()->getValue()])
			->setParameter('uuid', $game_id)
			->getQuery()
			->getResult(Query::HYDRATE_ARRAY);
	}

	/**
	 * @throws NonUniqueResultException
	 */
	public function getLatestPlayer(string $game_id, string $player_id): ?GamePlayer
	{
		return $this->createQueryBuilder('gp')
			->select('gp, gpm, p')
			->join('gp.GamePlayerMeta', 'gpm')
			->join('gpm.Player', 'p', Join::WITH, 'p.uuid = :player_uuid')
			->join('gp.Game', 'g')
			->join('g.GameMeta', 'g_meta', Join::WITH, 'g_meta.uuid = :game_uuid')
			->leftJoin(Game::class, 'g_latest', Join::WITH, 'g.Game = g_latest.Game AND g_latest.sequence > g.sequence')
			->setParameter('game_uuid', $game_id)
			->setParameter('player_uuid', $player_id)
			->where('g_latest.id IS NULL')
			->getQuery()
			->getOneOrNullResult();
	}
}