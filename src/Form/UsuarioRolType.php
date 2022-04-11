<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Form;

use CarlosChininin\App\Infrastructure\Security\Form\MenuPermissionType;
use Doctrine\ORM\EntityRepository;
use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Entity\UsuarioRol;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class UsuarioRolType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('permissions', CollectionType::class, [
                'required' => false,
                'label' => 'Permisos',
                'entry_type' => MenuPermissionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $builder
                ->add('rol', TextType::class, [
                    'required' => false,
                ])
                ->add('config', EntityType::class, [
                    'class' => Config::class,
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('config')
                            ->where('config.activo = TRUE')
                            ->orderBy('config.nombreCorto', 'ASC');
                    },
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UsuarioRol::class,
        ]);
    }
}
