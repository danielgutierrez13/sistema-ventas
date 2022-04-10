<?php

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\Producto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('descripcion')
            ->add('precio')
            ->add('stock')
            ->add('categoria')
            ->add('marca')
            ->add('unidadMedida')
            ->add('precioVenta')
            ->add('codigo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Producto::class,
        ]);
    }
}