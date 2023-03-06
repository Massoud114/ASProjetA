<?php

namespace App\Application\Campaign\Form;

use App\Application\Campaign\Promotion;
use App\Application\Product\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Dropzone\Form\DropzoneType;

class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', DropzoneType::class, [
	            'mapped' => false,
	            'label' => 'promotion.form.imageLabel',
	            'attr' => [
		            'accept' => 'image/*',
		            'placeholder' => 'dropzoneImage'
	            ],
	            'help' => 'promotion.form.imageHelp',
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
            ->add("private", CheckboxType::class, [
					            'label' => 'promotion.form.privateLabel',
	            'help' => 'promotion.form.privateHelp',
	            'required' => true,
            ])
            ->add('startAt', DateTimeType::class, [
	            'label' => 'promotion.form.startAtLabel',
	            'help' => 'promotion.form.startAtHelp',
	            'required' => true,
	            'widget' => 'single_text',
	            'html5' => false,
	            'attr' => [
		            'class' => 'js-datepicker',
		            'autocomplete' => 'off',
	            ],
			])
            ->add('endAt', DateTimeType::class, [
	            'label' => 'promotion.form.startAtLabel',
	            'help' => 'promotion.form.startAtHelp',
	            'required' => true,
	            'widget' => 'single_text',
	            'html5' => false,
	            'attr' => [
		            'class' => 'js-datepicker',
		            'autocomplete' => 'off',
	            ],
			])
            ->add('promoCode', TextType::class, [
	            'label' => 'promotion.form.promoCodeLabel',
	            'help' => 'promotion.form.promoCodeHelp',
	            'required' => false,
			])
            ->add('percentage', PercentType::class, [
	            'label' => 'promotion.form.percentageLabel',
	            'help' => 'promotion.form.percentageHelp',
	            'required' => true
            ])
            ->add('products', EntityType::class, [
	            'label' => 'promotion.form.productsLabel',
	            'help' => 'promotion.form.productsHelp',
	            'class' => Product::class,
	            'choice_label' => 'name',
	            'multiple' => true,
	            'expanded' => true,
	            'required' => true,
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class,
        ]);
    }
}
