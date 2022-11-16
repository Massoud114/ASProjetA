<?php

namespace App\Infrastructure\Twig;

use Twig\Environment;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Knp\Bundle\PaginatorBundle\Helper\Processor;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

class TwigPaginationExtension extends AbstractExtension
{
	public function __construct(private readonly Processor $processor) { }

	public function getFunctions(): array
	{
		return [
			new TwigFunction(
				'paginate',
				[$this, 'renderPaginate'],
				['is_safe' => ['html'], 'needs_environment' => true]
			),
			new TwigFunction(
				'sort_by',
				[$this, 'sortBy'],
				['is_safe' => ['html'], 'needs_environment' => true]
			),
		];
	}

	/**
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 * @throws \Twig\Error\LoaderError
	 */
	public function renderPaginate(
		Environment $env,
		SlidingPagination $pagination,
		array $queryParams = [],
		array $viewParams = [],
	): string
	{
		return $env->render(
			$pagination->getTemplate() ?: 'partials/pagination.html.twig',
			$this->processor->render($pagination, $queryParams, $viewParams)
		);
	}

	/**
	 * @throws \Twig\Error\SyntaxError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\LoaderError
	 */
	public function sortBy(
		Environment $env,
		SlidingPagination $pagination,
		string $title,
		string $key,
		array $options = [],
		array $params = [],
		?string $template = null
	): string
	{
		return $env->render(
			$template ?: (string) $pagination->getSortableTemplate(),
			$this->processor->sortable($pagination, $title, $key, $options, $params)
		);
	}
}
