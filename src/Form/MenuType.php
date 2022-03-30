<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Entity\Menu;
use Pidia\Apps\Demo\Security\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function count;

class MenuType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $menuActual = $builder->getData();
        $builder
            ->add('parent')
            ->add('name')
            ->add('route', ChoiceType::class, [
                'choices' => $this->items($menuActual),
                'required' => false,
            ])
            ->add('icon')
            ->add('rank')
            ->add('badge')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }

    private function items(?Menu $menuActual): array
    {
        $config = $this->security->config();
        if (0 !== count($config)) {
            $items = $this->security->repository(Config::class)->findMenusByConfigId($config['id']);
            $data = [];
            if (null !== $menuActual) {
                $data[$menuActual->getName()] = $menuActual->getRoute();
            }
            foreach ($items as $item) {
                $data[$item['name']] = $item['route'];
            }

            return $data;
        }

        return [];
    }
}
