<?php

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\ConfigMenu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigMenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre',
                'required' => true,
            ])
            ->add('route', TextType::class, [
                'label' => 'Ruta',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ConfigMenu::class,
        ]);
    }
}
