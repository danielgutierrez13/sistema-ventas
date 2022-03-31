<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\Menu;
use Pidia\Apps\Demo\Repository\ConfigRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class MenuType extends AbstractType
{
    public function __construct(
        private Security $security,
        private ConfigRepository $configRepository
    ) {
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
            ->add('ranking')
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
        $configId = $this->security->getUser()->config()?->getId();
        if (null === $configId) {
            return [];
        }

        $items = $this->configRepository->findMenusByConfigId($configId);

        $data = [];
        if (null !== $menuActual && null !== $menuActual->getId()) {
            $data[$menuActual->getName()] = $menuActual->getRoute();
        }

        foreach ($items as $item) {
            $data[$item['name']] = $item['route'];
        }

        return $data;
    }
}
