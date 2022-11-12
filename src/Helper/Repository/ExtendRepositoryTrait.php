<?php

namespace App\Helper\Repository;

trait ExtendRepositoryTrait
{
	public function save($entity, bool $flush = true): void
	{
		$this->add($entity, true);
	}
}
