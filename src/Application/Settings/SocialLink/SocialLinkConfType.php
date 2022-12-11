<?php

namespace App\Application\Settings\SocialLink;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class SocialLinkConfType extends AbstractType
{

	public function __construct(private readonly SocialLinkRepository $repository) {
	}

	public function buildForm(FormBuilderInterface $builder, array $options): void
    {
		foreach ($this->repository->findAll() as $socialLink){
			$builder
                ->add($socialLink->getName(), UrlType::class, [
					'label' => ucwords($socialLink->getName()),
	                'required' => false,
	                'help' => 'social_links.help',
	                'default_protocol' => 'https',
					'attr' => [
						'type' => 'url',
					],
	                'mapped' => false,
	                'data' => $socialLink->getUrl()
                ])
            ;
		}
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocialLink::class,
        ]);
    }
}
