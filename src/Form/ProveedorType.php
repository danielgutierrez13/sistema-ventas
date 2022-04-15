<?php

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\Categoria;
use Pidia\Apps\Demo\Entity\Proveedor;
use Pidia\Apps\Demo\Entity\TipoDocumento;
use Pidia\Apps\Demo\Entity\TipoPersona;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProveedorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'required' => true,
            ])
            ->add('documento', TextType::class, [
                'label' => 'Documento',
                'required' => true,
            ])
            ->add('direccion', TextType::class, [
                'label' => 'Direccion',
                'required' => false,
            ])
            ->add('telefono', TextType::class, [
                'label' => 'Telefono',
                'required' => false,
            ])
            ->add('tipoPersona', EntityType::class, [
                'class' => TipoPersona::class,
                'label' => 'Tipo Persona',
                'required' => true,
            ])
            ->add('tipoDocumento', EntityType::class, [
                'class' => TipoDocumento::class,
                'label' => 'Tipo Documento',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Proveedor::class,
        ]);
    }
}
