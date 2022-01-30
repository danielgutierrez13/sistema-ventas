<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Form;

use Doctrine\ORM\EntityRepository;
use Pidia\Apps\Demo\Entity\Menu;
use Pidia\Apps\Demo\Entity\UsuarioPermiso;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioPermisoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('menu', EntityType::class, [
                'class' => Menu::class,
                'required' => false,
                'placeholder' => 'Seleccione ...',
                'group_by' => 'padre',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        //->leftJoin('m.padre','p')
                        ->where('m.activo = :activo')
                        ->andWhere('m.padre IS NOT NULL')
                        ->setParameter('activo', true)
                        ->orderBy('m.orden', 'ASC');
                },
            ])
            ->add('listar')
            ->add('mostrar')
            ->add('crear')
            ->add('editar')
            ->add('eliminar')
            ->add('imprimir')
            ->add('exportar')
            ->add('importar')
            ->add('maestro')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UsuarioPermiso::class,
        ]);
    }
}
