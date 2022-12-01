<?php

namespace App\Application\Shop\Data;

use App\Application\Product\Entity\Category;

class SearchData
{
	public int $page = 1;

	public string $q = '';

	/**
	 * @var Category[]
	 */
	public array $categories = [];

	public int|null $max;

	public int|null $min;

	public bool $promo = false;
}
