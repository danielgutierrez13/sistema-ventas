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
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'required' => true,
            ])
            ->add('documento', TextType::class, [
                'label' => 'Documento',
                'required' => true,
            ])
            ->add('telefono', TextType::class, [
                'label' => 'Telefono',
                'required' => false,
            ])
            ->add('direccion', TextType::class, [
                'label' => 'Dirección',
                'required' => false,
            ])
            ->add('tipoDocumento', EntityType::class, [
                'label' => 'Tipo Documento',
                'class' => TipoDocumento::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('tipoDocumento')
                        ->where('tipoDocumento.activo = TRUE')
                        ->andWhere('tipoDocumento.tipoPersona = 2')
                    ;
                },
            ])
            ->add('username', TextType::class, [
                'label' => 'Usuario',
                'attr' => ['autocomplete' => 'off'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['autocomplete' => 'off'],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
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
