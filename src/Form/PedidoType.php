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
            ->add('precioFinal', TextType::class, [
                'label' => 'Precio Total',
            ])
            ->add('codigo', TextType::class, [
                'label' => 'Código',
            ])
            ->add('vendedor', EntityType::class, [
                'class' => Vendedor::class,
                'label' => 'Vendedor',
            ])
            ->add('cliente', EntityType::class, [
                'class' => Cliente::class,
                'label' => 'Cliente',
                'required' => false,
                'placeholder' => true,
            ])
            ->add('tipoPago', EntityType::class, [
                'class' => TipoPago::class,
                'label' => 'Modo de Pago',
                'required' => false,
                'placeholder' => false,
            ])
            ->add('tipoMoneda', EntityType::class, options: [
                'class' => TipoMoneda::class,
                'label' => 'Tipo de Moneda',
                'required' => false,
                'placeholder' => false,
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
                'label' => 'Detalles',
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
