<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Application\User\User;
use App\Application\Product\Product;
use App\Application\Purchase\Purchase;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Application\Purchase\Entity\PurchaseProduct;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PurchaseFixtures extends Fixture
{

	public function __construct(private readonly UserPasswordHasherInterface $hasher) { }

	public function load(ObjectManager $manager): void
    {
		$faker = Factory::create('fr_FR');

		$products = $manager->getRepository(Product::class)->findAll();
		$last = $manager->getRepository(Purchase::class)->getLatestOrderNumber();
		for ($i = 0; $i < 45; $i++) {
			$user = new User();
			$user
				->setCustomer(true)
				->setEmail($faker->email())
				->setFirstname($faker->firstName())
				->setUsername($faker->userName())
				->setlastname($faker->lastName())
				->setPhoneNumber($faker->phoneNumber())
				->setCountry('BJ')
				->setPassword($this->hasher->hashPassword($user, 'password'))
			;
			$manager->persist($user);

			/** @var Product[] $purchaseProducts */
			$purchaseProducts = [];
			$n = $faker->numberBetween(0, 5);
			for ($j = 0; $j < $n; $j++) {
			    $purchaseProducts[] = $products[$faker->numberBetween(1, count($products) - 1)];
			}

			$last++;
		    $purchase = new Purchase();
			$purchase
				->setOrderNumber($last)
				->setCustomer($user)
				->setConfirmed(false)
			;
			if ($faker->boolean(25)){
				$purchase->setPhoneNumber($faker->phoneNumber());
			}
			if ($faker->boolean(40)){
				$purchase->setNotes($faker->sentence(50));
			}
			foreach ($purchaseProducts as $product) {
				$purchaseProduct = new PurchaseProduct();
				$purchaseProduct
					->setPrice($product->getFixedPrice())
					->setQuantity($faker->numberBetween(0, 5))
					->setProduct($product)
					->setPurchase($purchase);
				$manager->persist($purchaseProduct);
				$purchase->addPurchaseProduct($purchaseProduct);
			}
			$manager->persist($purchase);

			dump($i);
		}

        $manager->flush();
    }
}
