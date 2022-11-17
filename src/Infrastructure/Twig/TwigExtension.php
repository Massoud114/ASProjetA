<?php

namespace App\Infrastructure\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class TwigExtension extends AbstractExtension
{
	public function getFunctions(): array
	{
		return [
			new TwigFunction('icon', [$this, 'nioicon'], ['is_safe' => ['html']]),
			new TwigFunction('menu_active', [$this, 'isMenuActive'], ['is_safe' => ['html'], 'needs_context' => true]),
		];
	}

	public function nioicon(string $name, ?string $type = null, string $class = null): string
	{
		$attr = '';
		if ($type) {
			$attr = "-{$type}";
		}
		if ($class) {
			$attr .= " {$class}";
		}
		return <<<HTML
		<em class="icon ni ni-{$name}{$attr}"></em>
		HTML;
	}

	public function isMenuActive(array $context, string $name): string
	{
		if (($context['menu'] ?? null) === $name) {
			return ' active current-page';
		}
		return '';
	}

}
