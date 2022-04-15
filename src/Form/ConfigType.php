<?php

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Entity\ConfigMenu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('alias', TextType::class, [
                'label' => 'Alias',
                'required' => true,
            ])
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'required' => true,
            ])
            ->add('nombreCorto', TextType::class, [
                'label' => 'Nombre Corto',
                'required' => true,
            ])
            ->add('menus', EntityType::class, [
                'class' => ConfigMenu::class,
                'label' => 'Menus',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Config::class,
        ]);
    }
}
