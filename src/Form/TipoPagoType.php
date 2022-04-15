<?php

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\TipoPago;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TipoPagoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('descripcion', TextType::class, [
                'label' => 'Descripción',
                'required' => true,
            ])
            ->add('propietarioCuenta', TextType::class, [
                'label' => 'Propietario Cuenta',
                'required' => false,
            ])
            ->add('cuenta', TextType::class, [
                'label' => 'Cuenta',
                'required' => false,
            ])
            ->add('nombreCorto', TextType::class, [
                'label' => 'nombre Corto',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TipoPago::class,
        ]);
    }
}
