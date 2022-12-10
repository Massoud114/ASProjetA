<?php

namespace App\Admin\Controller;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

	protected function back(): RedirectResponse
	{
		return $this->redirect($this->request->headers->get('referer'));
	}

	protected function getRedirectUrl(Request $request, UrlGeneratorInterface $urlGenerator): string
	{
		$referer = $request->headers->get('referer');
		return str_contains($referer, 'show') || str_contains($referer, 'edit') ?
			$urlGenerator->generate($this->routePrefix . '_index') :
			$request->headers->get('referer');
	}

	protected function redirectBack(string $route, array $params = []): RedirectResponse
	{
		/** @var RequestStack $stack */
		$stack = $this->get('request_stack');
		$request = $stack->getCurrentRequest();
		if ($request && $request->server->get('HTTP_REFERER')) {
			return $this->redirect($request->server->get('HTTP_REFERER'));
		}

		return $this->redirectToRoute($route, $params);
	}
}
