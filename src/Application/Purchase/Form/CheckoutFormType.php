<?php

namespace App\Application\Purchase\Form;

use App\Application\Purchase\Purchase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CheckoutFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phoneNumber', TelType::class, [
                'label' => "phone_number",
	            'help' => "phone_number.to_be_used_to_contact",
	            'required' => false
            ])
	        ->add('notes', TextareaType::class, [
		        'label' => 'notes',
		        'help' => 'notesHelp',
		        'required' => false,
		        'attr' => [
			        'data-controller' => 'textarea-autogrow',
		        ]
	        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Purchase::class,
        ]);
    }
}
