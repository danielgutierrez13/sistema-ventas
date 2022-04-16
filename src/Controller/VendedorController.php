<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Pidia\Apps\Demo\Entity\Usuario;
use Pidia\Apps\Demo\Entity\Vendedor;
use Pidia\Apps\Demo\Form\VendedorType;
use Pidia\Apps\Demo\Manager\UsuarioManager;
use Pidia\Apps\Demo\Manager\VendedorManager;
use Pidia\Apps\Demo\Repository\UsuarioRolRepository;
use Pidia\Apps\Demo\Repository\VendedorRepository;
use Pidia\Apps\Demo\Security\PasswordSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/vendedor')]
class VendedorController extends WebAuthController
{
    public const BASE_ROUTE = 'vendedor_index';

    #[Route(path: '/', name: 'vendedor_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'vendedor_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, VendedorManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'vendedor/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'vendedor_export', methods: ['GET'])]
    public function export(Request $request, VendedorManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'nombre' => 'Nombre de Vendedor',
            'tipoDocumento.descripcion' => 'Tipo de Documento',
            'documento' => 'N° de Documento',
            'direccion' => 'Direccion',
            'telefono' => 'Telefono',
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'vendedor');
    }

    #[Route(path: '/new', name: 'vendedor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VendedorManager $manager, PasswordSecurity $authPassword, EntityManagerInterface $entityManager, UsuarioRolRepository $rolRepository): Response
    {
        $this->denyAccess([Permission::NEW]);
        $vendedor = new Vendedor();
        $form = $this->createForm(VendedorType::class, $vendedor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $usuario = new Usuario();
                $usuario->setUsername($vendedor->getUsername());
                $usuario->setEmail($vendedor->getEmail());
                $usuario->setFullName($vendedor->getNombre());
                $usuario->setPropietario($this->getUser());
                $usuario->setPassword($authPassword->encrypt($usuario, $vendedor->getPassword()));
                $usuario->addUsuarioRole($rolRepository->UsuarioRolId(2));

                $entityManager->persist($usuario);
                $entityManager->flush();

                $vendedor->setUsuario($usuario);
            } catch (DuplicateKeyException) {
                $this->addFlash('danger', 'Existe un email o usuario repetido');
            } catch (Exception) {
                $this->addFlash('danger', 'Se ha producido un error');
            }

            $vendedor->setPropietario($this->getUser());
            $vendedor->setUsuario($usuario);

            if ($manager->save($vendedor)) {
                $this->messageSuccess('Registro creado!!!');

                return $this->redirectToRoute('vendedor_index');
            }
            $this->addErrors($manager->errors());
        }

        return $this->renderForm(
            'vendedor/new.html.twig',
            [
                'vendedor' => $vendedor,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'vendedor_show', methods: ['GET'])]
    public function show(Vendedor $vendedor): Response
    {
        $this->denyAccess([Permission::SHOW], $vendedor);

        return $this->render('vendedor/show.html.twig', ['vendedor' => $vendedor]);
    }

    #[Route(path: '/{id}/edit', name: 'vendedor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vendedor $vendedor, VendedorManager $manager, PasswordSecurity $authPassword, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccess([Permission::EDIT], $vendedor);

        $form = $this->createForm(VendedorType::class, $vendedor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $usuario = $vendedor->getUsuario();
                $usuario->setUsername($vendedor->getUsername());
                $usuario->setEmail($vendedor->getEmail());
                $usuario->setFullName($vendedor->getNombre());
                $usuario->setPassword($authPassword->encrypt($usuario, $vendedor->getPassword()));
                $entityManager->persist($usuario);
                $entityManager->flush();

            } catch (DuplicateKeyException) {
                $this->addFlash('danger', 'Existe un email o usuario repetido');
            } catch (Exception) {
                $this->addFlash('danger', 'Se ha producido un error');
            }
            if ($manager->save($vendedor)) {
                $this->messageSuccess('Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('vendedor_index', ['id' => $vendedor->getId()]);
        }

        return $this->render(
            'vendedor/edit.html.twig',
            [
                'vendedor' => $vendedor,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'vendedor_delete', methods: ['POST'])]
    public function state(Request $request, Vendedor $vendedor, VendedorManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $vendedor);

        if ($this->isCsrfTokenValid('delete'.$vendedor->getId(), $request->request->get('_token'))) {
            $usuario = $vendedor->getUsuario();
            $usuario->changeActivo();
            $vendedor->changeActivo();
            if ($manager->save($vendedor)) {
                $this->messageSuccess('Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('vendedor_index');
    }

    #[Route(path: '/{id}/delete', name: 'vendedor_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, Vendedor $vendedor, VendedorManager $manager, UsuarioManager $usuarioManager): Response
    {
        $this->denyAccess([Permission::DELETE], $vendedor);
        $usuario = $vendedor->getUsuario();
        if ($this->isCsrfTokenValid('delete_forever'.$vendedor->getId(), $request->request->get('_token'))) {
            if ($manager->remove($vendedor)) {
                $this->messageWarning('Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
            if ($usuarioManager->remove($usuario)) {
                $this->messageWarning('Usuario Eliminado eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('vendedor_index');
    }

    #[Route('/busqueda/usuario', name: 'usuario_for_vendedor')]
    public function busquedaUsuariVendedor(Request $request, VendedorRepository $vendedorRepository): Response
    {
        $documento = $request->request->get('documentoVendedor');
        $vendedor = $vendedorRepository->buscarUsuarioVendedor($documento);
        if (null === $vendedor) {
            return $this->json(['status' => false, 'message' => 'No se encontro el vendedor']);
        }
        $usuario = $vendedor->getUsuario();
        if (null === $usuario) {
            return $this->json(['status' => false, 'message' => 'No se encontro usuario']);
        }

        $data = [
            'usernameV' => $usuario->getUsername(),
            'emailV' => $usuario->getEmail(),
        ];

        return $this->json(['status' => true, 'data' => $data]);
    }
}
