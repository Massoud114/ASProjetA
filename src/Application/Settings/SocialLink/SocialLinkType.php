<?php

namespace App\Application\Settings\SocialLink;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SocialLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
				'label' => 'social_links.name',
	            'required' => true
            ])
            ->add('icon', TextType::class, [
				'label' => 'social_links.icon',
	            'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocialLink::class,
        ]);
    }
}
