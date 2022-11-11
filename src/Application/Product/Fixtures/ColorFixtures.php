<?php

namespace App\Application\Product\Fixtures;

use Doctrine\Persistence\ObjectManager;
use App\Application\Product\Entity\Color;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ColorFixtures extends Fixture
{
	public function __construct(private readonly HttpClientInterface $httpClient) { }


	/**
	 * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
	 */
	public function load(ObjectManager $manager): void
    {
		$request = $this->httpClient->request(Request::METHOD_GET, 'https://gist.githubusercontent.com/Massoud114/8a77b2fcec7155914e4a2cb7e45f40d1/raw/8839f01c2c2d31758c71e2bb9f59da33bc9581a3/colors.json');
		$response = $request->toArray();

		foreach ($response as $colorName => $hexColor) {
			$color = new Color();
			$color->setName($colorName);
			$color->setHexColor(str_replace('#', '', $hexColor));
			$manager->persist($color);
		}

        $manager->flush();
    }
}
