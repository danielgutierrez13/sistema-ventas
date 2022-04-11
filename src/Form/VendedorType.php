<?php

namespace Pidia\Apps\Demo\Form;

use Doctrine\ORM\EntityRepository;
use Pidia\Apps\Demo\Entity\TipoDocumento;
use Pidia\Apps\Demo\Entity\Vendedor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VendedorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class)
            ->add('documento', TextType::class)
            ->add('telefono', TextType::class, [
                'required' => false,
            ])
            ->add('direccion', TextType::class, [
                'required' => false,
            ])
            ->add('tipoDocumento', EntityType::class, [
                'class' => TipoDocumento::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('tipoDocumento')
                        ->where('tipoDocumento.activo = TRUE')
                        ->andWhere('tipoDocumento.tipoPersona = 2')
                    ;
                },
            ])
            ->add('username', TextType::class, [
                'attr' => ['autocomplete' => 'off'],
            ])
            ->add('email', EmailType::class, [
                'attr' => ['autocomplete' => 'off'],
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['autocomplete' => 'off'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vendedor::class,
        ]);
    }
}
