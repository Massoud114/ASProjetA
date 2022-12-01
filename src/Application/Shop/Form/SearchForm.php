<?php

namespace App\Application\Shop\Form;

use Symfony\Component\Form\AbstractType;
use App\Application\Shop\Data\SearchData;
use App\Application\Product\Entity\Category;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use function Symfony\Component\Translation\t;

class SearchForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', TextType::class, [
				'label' => 'product.search',
	            'attr' => [
					'placeholder' => t("product.search")
	            ],
	            'required' => false,
            ])
            ->add('categories', EntityType::class, [
				'label' => false,
	            'required' => false,
                'class' => Category::class,
	            'expanded' => true,
	            'multiple' => true
            ])
            ->add('min', NumberType::class, [
				'label' => false,
	            'required' => false,
	            'attr' => [
					'placeholder' => t('minPrice')
	            ]
            ])
            ->add('max', NumberType::class, [
				'label' => false,
	            'required' => false,
	            'attr' => [
					'placeholder' => t('maxPrice')
	            ]
            ])
            ->add('promo', CheckboxType::class, [
				'label' => 'isPromo',
	            'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
	        'method' => 'GET',
	        'csrf_protection' => false
        ]);
    }

	public function getBlockPrefix(): string
	{
		return '';
	}
}
