<?php

namespace App\Application\Product\Form;

use App\Application\Product\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\UX\Dropzone\Form\DropzoneType;
use App\Application\Product\Entity\Category;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use App\Infrastructure\Type\SearchableEntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SimpleProductType extends AbstractType
{
	public function __construct(private readonly UrlGeneratorInterface $urlGenerator) { }

	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('name', TextType::class, [
				'label' => 'product.form.name',
				'attr' => [],
				'required' => true,
				'help' => 'product.form.nameHelp'
			])
			->add('type', ChoiceType::class, [
				'label' => 'product.form.types',
				'choices' => Product::TYPES,
				'multiple' => false,
				'expanded' => true,
				'attr' => [
					'selected' => Product::TYPES['single'],
				],
				'choice_attr' => function ($choice, $key, $value) {
                    return [
						'class' => "custom-control-input",
	                    "data-action" => "change->custom-choice#change",
                    ];
                },
				'label_attr' => ['class' => "custom-control-label"],
				'required' => true,
			])
			->add('stockQuantity', NumberType::class, [
				'label' => 'stockQuantity',
				'attr' => [],
				'help' => 'stockQuantityHelp',
				'required' => true,
			])
			->add('makingPrice', NumberType::class, [
				'label' => 'makingPrice',
				'html5' => true,
				'attr' => [],
				'required' => false,
			])
			->add('fixedPrice', NumberType::class, [
				'label' => 'product.form.fixedPrice',
				'html5' => true,
				'attr' => [],
				'required' => true,
				'help' => 'product.form.fixedPriceHelp',
			])
			->add('categories', SearchableEntityType::class, [
				'class' => Category::class,
				'search' => $this->urlGenerator->generate('api_category_list'),
				'label_property' => 'name',
				'value_property' => 'id'
			])
			->add('thumbnailUrl', DropzoneType::class, [
				'mapped' => false,
				'label' => 'product.form.imageLabel',
				'attr' => [
					'accept' => 'image/*',
					'placeholder' => 'dropzoneImage'
				],
				'required' => true,
				'help' => 'product.form.imageHelp',
				'multiple' => false,
				'constraints' => [
					new File([
						'maxSize' => '10240k',
						'mimeTypes' => [
							'image/jpeg',
							'image/png',
							'image/gif',
							'image/svg+xml',
							'image/webp',
							'image/jpg',
						],
					])
				],
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Product::class,
			'method' => 'POST',
		]);
	}
}
