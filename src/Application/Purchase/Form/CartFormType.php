<?php

namespace App\Application\Purchase\Form;

use Symfony\Component\Form\AbstractType;
use App\Application\Purchase\Entity\Cart;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CartFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('items', CollectionType::class, [
				'entry_type' => AddItemToCartFormType::class,
				/*'entry_options' => [
					'product' => null,
				],*/
				'allow_add' => false,
				'allow_delete' => false,
//				'by_reference' => false,
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cart::class,
        ]);
    }
}
