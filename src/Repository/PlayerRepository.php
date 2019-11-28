<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Player;
use App\Games\Duizenden\Entity\Game;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Player findOneByUuid(string $uuid)
 */
final class PlayerRepository extends EntityRepository
{
	/**
	 * @param string[] $uuids
	 *
	 * @return Player[]
	 */
	public function findIndexedByPlayedIds(array $uuids): array
	{
		return $this->createQueryBuilder('p', 'p.uuid')
			->where('p.uuid IN (:uuids)')
			->setParameter('uuids', $uuids)
			->getQuery()
			->execute();
	}

	/**
	 * @throws NonUniqueResultException
	 */
	public function findAnonymousPlayer(string $uuid): ?Player
	{
		return $this->createQueryBuilder('p')
			->where('p.is_registered = false')
			->andWhere('p.uuid = :uuid')
			->setParameter('uuid', $uuid)
			->getQuery()
			->getOneOrNullResult();
	}

	public function findCurrentRoundDealer(string $game_id): ?Player
	{
		return $this->createQueryBuilder('p, gpms, gps, g, g_latest')
			->join('p.GamePlayerMetas', 'gpms')
			->join('gpms.GamePlayers', 'gps')
			->join('gps.CurrentPlayerGames', 'g')
			->join('g.GameMeta', 'g_meta', Join::WITH, 'g_meta.uuid = :game_id')
			->leftJoin(Game::class, 'g_latest', Join::WITH, 'g.Game = g_latest.Game AND g_latest.sequence > g.sequence')
			->where('g_latest.id IS NULL')
			->setParameter('game_id', $game_id)
			->getQuery()
			->getResult();
	}
}