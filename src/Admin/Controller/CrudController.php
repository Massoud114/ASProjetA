<?php

namespace App\Admin\Controller;

use Doctrine\ORM\QueryBuilder;

abstract class CrudController extends BaseController
{

	protected function applySearch(string $search, QueryBuilder $query, array $searchFields): QueryBuilder
	{
		if (empty($search)) {
			return $query;
		}
		$query->where("row.{$searchFields[0]} LIKE :search");
		if (count($searchFields) !== 1) {
			for ($i = 1; $i < count($searchFields); $i++) {
				$query->orWhere("row.{$searchFields[$i]} LIKE :search");
			}
		}
		return $query
			->setParameter('search', '%'.strtolower($search).'%');
	}
}
