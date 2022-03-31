<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Pidia\Apps\Demo\Entity\Usuario;
use Pidia\Apps\Demo\Form\UsuarioType;
use Pidia\Apps\Demo\Manager\UsuarioManager;
use Pidia\Apps\Demo\Security\PasswordSecurity;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/usuario')]
final class UsuarioController extends WebAuthController
{
    public const BASE_ROUTE = 'usuario_index';

    #[Route(path: '/', name: 'usuario_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'usuario_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, UsuarioManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render('usuario/index.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    #[Route(path: '/export', name: 'usuario_export', methods: ['GET'])]
    public function export(Request $request, UsuarioManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'usuario' => 'Usuario',
            'nombres' => 'Nombres',
            'correo' => 'Correo',
            'roles' => 'Roles',
            'activo' => 'Activo',
        ];

        $objetos = $manager->dataExport(ParamFetcher::fromRequestQuery($request));
        $data = [];
        /** @var Usuario $objeto */
        foreach ($objetos as $objeto) {
            $item = [];
            $item['usuario'] = $objeto->getUsername();
            $item['nombres'] = $objeto->getFullName();
            $item['correo'] = $objeto->getEmail();

            $item['roles'] = '';
            foreach ($objeto->getUsuarioRoles() as $usuarioRol) {
                $item['roles'] = $usuarioRol->getNombre().', '.$item['roles'];
            }

            $item['activo'] = $objeto->activo();
            $data[] = $item;
            unset($item);
        }

        return $manager->export($data, $headers, 'usuario');
    }

    #[Route(path: '/new', name: 'usuario_new', methods: 'GET|POST')]
    public function new(Request $request, PasswordSecurity $authPassword, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccess([Permission::NEW]);

        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $usuario->setPropietario($this->getUser());
            try {
                $usuario->setPassword($authPassword->encrypt($usuario, $usuario->getPassword()));
                $entityManager->persist($usuario);
                $entityManager->flush();

                return $this->redirectToRoute('usuario_index');
            } catch (DuplicateKeyException) {
                $this->addFlash('danger', 'Existe un email o usuario repetido');
            } catch (Exception) {
                $this->addFlash('danger', 'Se ha producido un error');
            }
        }

        return $this->render('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}', name: 'usuario_show', methods: 'GET')]
    public function show(Usuario $usuario): Response
    {
        $this->denyAccess([Permission::SHOW], $usuario);

        return $this->render('usuario/show.html.twig', ['usuario' => $usuario]);
    }

    #[Route(path: '/{id}/edit', name: 'usuario_edit', methods: 'GET|POST')]
    public function edit(Request $request, Usuario $usuario, PasswordSecurity $authPassword, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccess([Permission::EDIT], $usuario);

        $passwordOriginal = $usuario->getPassword();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $usuario->getPassword() && '' !== $usuario->getPassword()) {
                $usuario->setPassword($authPassword->encrypt(Usuario::class, $usuario->getPassword()));
            } else {
                $usuario->setPassword($passwordOriginal);
            }

            $entityManager->flush();

            return $this->redirectToRoute('usuario_index', ['id' => $usuario->getId()]);
        }

        return $this->render('usuario/edit.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}', name: 'usuario_delete', methods: 'DELETE')]
    public function delete(Request $request, Usuario $usuario, UsuarioManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $usuario);

        if ($this->isCsrfTokenValid('delete'.$usuario->getId(), $request->request->get('_token'))) {
            $usuario->changeActivo();
            if ($manager->save($usuario)) {
                $this->addFlash('warning', 'Registro actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('usuario_index');
    }

    #[Route(path: '/{id}/delete', name: 'usuario_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, Usuario $usuario, UsuarioManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $usuario);
        if ($this->isCsrfTokenValid('delete_forever'.$usuario->getId(), $request->request->get('_token'))) {
            if ($manager->remove($usuario)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('usuario_index');
    }

    #[Route(path: '/{id}/profile', name: 'usuario_profile', methods: 'GET')]
    public function profile(Usuario $usuario): Response
    {
        $this->denyAccess([Permission::SHOW], $usuario);

        return $this->render('usuario/profile.html.twig', [
            'usuario' => $usuario,
        ]);
    }
}
