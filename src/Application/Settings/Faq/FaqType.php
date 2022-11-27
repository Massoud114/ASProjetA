<?php

namespace App\Application\Settings\Faq;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FaqType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextareaType::class, [
	            'label' => 'faq.question',
	            'attr' => [
//		            'class' => 'rich-text-editor',
		            'rows' => 5,
	            ],
	            'help' => 'faq.question.help'
            ])
            ->add('answer', null, [
	            'label' => 'faq.answer',
	            'attr' => [
		            'class' => 'rich-text-editor',
		            'rows' => 7,
	            ],
	            'help' => 'faq.answer.help'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Faq::class,
        ]);
    }
}
