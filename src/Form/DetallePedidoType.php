<?php

namespace Pidia\Apps\Demo\Form;

use Doctrine\ORM\EntityRepository;
use Pidia\Apps\Demo\Entity\DetallePedido;
use Pidia\Apps\Demo\Entity\Producto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetallePedidoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cantidad')
            ->add('precio', NumberType::class, [
                'label' => 'Precio',
                'required' => true,
            ])
            ->add('descuento', NumberType::class, [
                'label' => 'Descuento',
                'required' => true,
            ])
            ->add('producto', EntityType::class, [
                'class' => Producto::class,
                'required' => true,
                'placeholder' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('producto')
                        ->where('producto.stock > 0');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DetallePedido::class,
        ]);
    }
}
