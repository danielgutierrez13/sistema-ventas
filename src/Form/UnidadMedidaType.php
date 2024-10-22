<?php

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\UnidadMedida;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class UnidadMedidaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('descripcion', TextType::class, [
                'label' => 'Unidad Medida',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UnidadMedida::class,
        ]);
    }
}
