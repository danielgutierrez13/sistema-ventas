<?php

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\Cliente;
use Pidia\Apps\Demo\Entity\TipoDocumento;
use Pidia\Apps\Demo\Entity\TipoPersona;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClienteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class)
            ->add('documento', TextType::class)
            ->add('direccion', TextType::class, [
                'required' => false,
            ])
            ->add('telefono', TextType::class, [
                'required' => false,
            ])
            ->add('tipoPersona', EntityType::class, [
                'class' => TipoPersona::class,
                'required' => true,
                'placeholder' => false,
            ])
            ->add('tipoDocumento', EntityType::class, [
                'class' => TipoDocumento::class,
                'required' => true,
                'placeholder' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cliente::class,
        ]);
    }
}
