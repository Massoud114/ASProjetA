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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductType extends AbstractType
{
	public function __construct(private UrlGeneratorInterface $urlGenerator) { }

	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('name', TextType::class, [
				'label' => 'product.form.name',
				'attr' => [],
				'required' => true,
				'help' => 'product.form.nameHelp'
			])
			->add('description', TextareaType::class, [
				'label' => 'product.form.description',
				'help' => 'product.form.descriptionHelp',
				'required' => true,
			])
			->add('details', null, [
				'help' => 'product.form.detailsHelp',
				'required' => false,
				'attr' => [
					'class' => 'rich-text-editor',
					'rows' => 7,
				]
			])
			->add('type', ChoiceType::class, [
				'label' => 'product.form.types',
				'choices' => Product::TYPES,
				'multiple' => false,
				'attr' => [
					'selected' => Product::TYPES['single'],
				],
				'required' => true,
			])
			->add('minPrice', NumberType::class, [
				'label' => 'minPrice',
				'html5' => true,
				'attr' => [],
				'required' => false,
			])
			->add('maxPrice', NumberType::class, [
				'label' => 'maxPrice',
				'attr' => [],
				'html5' => true,
				'required' => false,
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
			->add('weight', NumberType::class, [
				'label' => 'weight',
				'html5' => true,
				'attr' => [
					'step' => 0.01,
				],
				'required' => false,
			])
			->add('weightUnit', ChoiceType::class, [
				'label' => 'weightUnit',
				'choices' => Product::WEIGHT_UNITS,
				'attr' => [
					'selected' => Product::WEIGHT_UNITS['g'],
				],
				'required' => false,
			])
			->add('brand', TextType::class, [
				'label' => 'product.form.brand',
				'attr' => [],
				'help' => 'product.form.brandHelp',
				'required' => false,
			])
			->add('thickness', NumberType::class, [
				'label' => 'thickness',
				'html5' => true,
				'attr' => [
					'step' => 0.01,
				],
				'required' => false,
			])
			->add('thicknessUnit', ChoiceType::class, [
				'label' => 'thicknessUnit',
				'choices' => Product::THICKNESS_UNITS,
				'attr' => [
					'selected' => Product::THICKNESS_UNITS['cm'],
				],
				'required' => false,
			])
			->add('width', NumberType::class, [
				'label' => 'width',
				'html5' => true,
				'attr' => [
					'step' => 0.01,
				],
				'required' => false,
			])
			->add('widthUnit', ChoiceType::class, [
				'label' => 'widthUnit',
				'choices' => Product::THICKNESS_UNITS,
				'attr' => [
					'selected' => Product::THICKNESS_UNITS['cm'],
				],
				'required' => false,
			])
			->add('height', NumberType::class, [
				'label' => 'height',
				'html5' => true,
				'attr' => [
					'step' => 0.01,
				],
				'required' => false,
			])
			->add('heightUnit', ChoiceType::class, [
				'label' => 'heightUnit',
				'choices' => Product::THICKNESS_UNITS,
				'attr' => [
					'selected' => Product::THICKNESS_UNITS['cm'],
				],
				'required' => false,
			])
			->add('length', NumberType::class, [
				'label' => 'length',
				'html5' => true,
				'attr' => [
					'step' => 0.01,
				],
				'required' => false,
			])
			->add('lengthUnit', ChoiceType::class, [
				'label' => 'lengthUnit',
				'choices' => Product::THICKNESS_UNITS,
				'attr' => [
					'selected' => Product::THICKNESS_UNITS['cm'],
				],
				'required' => false,
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
				'help' => 'product.form.imageHelp',
				'multiple' => false,
				'required' => $options['data']->getId() === null,
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
			'sanitize_html' => true,
		]);
	}
}
