<?php

namespace App\Games\Duizenden\Repository;

use App\Games\Duizenden\Entity\GameMeta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

final class GameMetaRepository extends ServiceEntityRepository
{
	/**
	 * @inheritDoc
	 */
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, GameMeta::class);
	}
}