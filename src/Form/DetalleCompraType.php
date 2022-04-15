<?php

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\DetalleCompra;
use Pidia\Apps\Demo\Entity\Producto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetalleCompraType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('precio', NumberType::class, [
                'label' => 'Precio',
                'required' => true,
            ])
            ->add('cantidad', NumberType::class, [
                'label' => 'Cantidad',
                'required' => true,
            ])
            ->add('producto', EntityType::class, [
                'class' => Producto::class,
                'label' => 'Producto',
                'placeholder' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DetalleCompra::class,
        ]);
    }
}
