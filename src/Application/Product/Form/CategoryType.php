<?php

namespace App\Application\Product\Form;

use Symfony\Component\Form\AbstractType;
use App\Application\Product\Entity\Category;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
				'label' => 'category.name',
	            'required' => true
            ])
            ->add('parent', EntityType::class, [
				'class' => Category::class,
	            'required' => false,
	            'label' => 'category.parent',
	            'help' => 'category.parent.help'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
