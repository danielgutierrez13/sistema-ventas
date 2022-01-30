<?php

namespace Pidia\Apps\Demo\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Pidia\Apps\Demo\Entity\Usuario;
use Pidia\Apps\Demo\Entity\UsuarioRol;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager)
    {
        $role = new UsuarioRol();
        $role->setNombre('Super Administrador');
        $role->setRol('ROLE_SUPER_ADMIN');
        $manager->persist($role);

        $user = new Usuario();
        $user->setUsername('admin')->setFullname('Carlos Chininin');
        $user->setPassword($this->passwordHasher->hashPassword($user, '123456'));
        $user->setEmail('cio@pidia.pe');
        $user->addUsuarioRole($role);
        $manager->persist($user);

        $manager->flush();
    }
}
