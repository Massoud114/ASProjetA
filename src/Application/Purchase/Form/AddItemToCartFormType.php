<?php

namespace App\Application\Purchase\Form;

use App\Application\Product\Product;
use Symfony\Component\Form\AbstractType;
use App\Application\Product\Entity\Color;
use App\Application\Purchase\Entity\CartItem;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AddItemToCartFormType extends AbstractType
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $product = $options['product'];
        if (!$product instanceof Product) {
            throw new \InvalidArgumentException('The product option should be a Product object');
        }

        // sets a default data to use
        if (!($options['data'] ?? null)) {
            $builder->setData(new CartItem($product));
        }

        // set the action attribute for this form
        $builder->setAction($this->router->generate(
            'app_product_show',
            ['slug' => $product->getSlug()]
        ));

        $builder
            ->add('quantity', IntegerType::class, [
				'data' => 1,
            ])
        ;

        if ($product->hasColors()) {
            $builder->add('color', EntityType::class, [
                'class' => Color::class,
                'choices' => $product->getColors(),
                'placeholder' => 'Color',
                'choice_label' => 'name',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CartItem::class,
        ]);
        $resolver->setRequired(['product']);
    }
}
