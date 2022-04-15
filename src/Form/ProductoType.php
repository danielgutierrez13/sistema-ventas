<?php

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\Categoria;
use Pidia\Apps\Demo\Entity\Marca;
use Pidia\Apps\Demo\Entity\Producto;
use Pidia\Apps\Demo\Entity\UnidadMedida;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('descripcion', TextType::class, [
                'label' => 'Descripción',
                'required' => true,
            ])
            ->add('precio', NumberType::class, [
                'label' => 'Precio',
                'required' => true,
            ])
            ->add('stock', NumberType::class, [
                'label' => 'Stock',
                'required' => true,
            ])
            ->add('categoria', EntityType::class, [
                'class' => Categoria::class,
                'label' => 'Categoria',
                'required' => true,
            ])
            ->add('marca', EntityType::class, [
                'class' => Marca::class,
                'label' => 'Marca',
                'required' => true,
            ])
            ->add('unidadMedida', EntityType::class, [
                'class' => UnidadMedida::class,
                'label' => 'Unidad Medida',
                'required' => true,
            ])
            ->add('precioVenta', NumberType::class, [
                'label' => 'Precio Venta',
                'required' => true,
            ])
            ->add('codigo', TextType::class, [
                'label' => 'Codigo',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Producto::class,
        ]);
    }
}
