<?php

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\Cliente;
use Pidia\Apps\Demo\Entity\Pedido;
use Pidia\Apps\Demo\Entity\TipoMoneda;
use Pidia\Apps\Demo\Entity\TipoPago;
use Pidia\Apps\Demo\Entity\Vendedor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PedidoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('precioFinal', TextType::class, options: [
                'label' => 'Precio Total',
            ])
            ->add('codigo')
            ->add('vendedor', EntityType::class, options: [
                'class' => Vendedor::class,
                'label' => 'Vendedor',
                'placeholder' => true,
            ])
            ->add('cliente', EntityType::class, options: [
                'class' => Cliente::class,
                'required' => false,
                'label' => 'Cliente',
            ])
            ->add('tipoPago', EntityType::class, options: [
                'class' => TipoPago::class,
                'required' => false,
                'label' => 'Tipo de Pago',
            ])
            ->add('tipoMoneda', EntityType::class, options: [
                'class' => TipoMoneda::class,
                'required' => false,
                'label' => 'Tipo de Moneda',
            ])
            ->add('cambio', TextType::class, options: [
                'label' => 'Cambio',
                'required' => false,
            ])
            ->add('cantidadRecibida', TextType::class, options: [
                'label' => 'Cantidad Recibida',
                'required' => false,
            ])
            ->add('detallePedidos', CollectionType::class, [
                'entry_type' => DetallePedidoType::class,
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
            'data_class' => Pedido::class,
        ]);
    }
}
