<?php

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\TipoDocumento;
use Pidia\Apps\Demo\Entity\TipoPersona;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TipoDocumentoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('descripcion')
            ->add('tipoPersona', EntityType::class, [
                'class' => TipoPersona::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TipoDocumento::class,
        ]);
    }
}
