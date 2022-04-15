<?php

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\Compra;
use Pidia\Apps\Demo\Entity\Proveedor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompraType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codigo', TextType::class, [
                'label' => 'Código',
                'disabled' => true,
            ])
            ->add('precio', NumberType::class, [
                'label' => 'Precio Final',
                'disabled' => true,
            ])
            ->add('proveedor', EntityType::class, [
                'class' => Proveedor::class,
                'label' => 'Proveedor',
                'placeholder' => true,
            ])
            ->add('detalleCompras', CollectionType::class, [
                'label' => 'Detalles',
                'entry_type' => DetalleCompraType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Compra::class,
        ]);
    }
}
