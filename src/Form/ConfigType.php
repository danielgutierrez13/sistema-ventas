<?php

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Entity\ConfigMenu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('alias')
            ->add('nombre')
            ->add('nombreCorto')
            ->add('menus', EntityType::class, [
                'class' => ConfigMenu::class,
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
