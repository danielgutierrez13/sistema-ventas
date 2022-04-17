<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Pidia\Apps\Demo\Entity\Proveedor;
use Pidia\Apps\Demo\Entity\TipoDocumento;
use Pidia\Apps\Demo\Entity\TipoPersona;
use Pidia\Apps\Demo\Form\ProveedorType;
use Pidia\Apps\Demo\Manager\ProveedorManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/proveedor')]
class ProveedorController extends WebAuthController
{
    public const BASE_ROUTE = 'proveedor_index';

    #[Route(path: '/', name: 'proveedor_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'proveedor_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, ProveedorManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'proveedor/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'proveedor_export', methods: ['GET'])]
    public function export(Request $request, ProveedorManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'nombre' => 'Nombre',
            'tipoPersona.descripcion' => 'Tipo de Persona',
            'tipoDocumento.descripcion' => 'Tipo de Documento',
            'documento' => 'N° de Documento',
            'direccion' => 'Direccion',
            'telefono' => 'Telefono',
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'proveedor');
    }

    #[Route(path: '/new', name: 'proveedor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProveedorManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);
        $proveedor = new Proveedor();
        $form = $this->createForm(ProveedorType::class, $proveedor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($proveedor)) {
                $this->messageSuccess('Registro creado!!!');

                return $this->redirectToRoute('proveedor_index');
            }

            $this->addErrors($manager->errors());
        }

        return $this->renderForm(
            'proveedor/new.html.twig',
            [
                'proveedor' => $proveedor,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'proveedor_show', methods: ['GET'])]
    public function show(Proveedor $proveedor): Response
    {
        $this->denyAccess([Permission::SHOW], $proveedor);

        return $this->render('proveedor/show.html.twig', ['proveedor' => $proveedor]);
    }

    #[Route(path: '/{id}/edit', name: 'proveedor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Proveedor $proveedor, ProveedorManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $proveedor);

        $form = $this->createForm(ProveedorType::class, $proveedor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($proveedor)) {
                $this->messageSuccess('Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('proveedor_index', ['id' => $proveedor->getId()]);
        }

        return $this->render(
            'proveedor/edit.html.twig',
            [
                'proveedor' => $proveedor,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'proveedor_delete', methods: ['POST'])]
    public function state(Request $request, Proveedor $proveedor, ProveedorManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $proveedor);

        if ($this->isCsrfTokenValid('delete'.$proveedor->getId(), $request->request->get('_token'))) {
            $proveedor->changeActivo();
            if ($manager->save($proveedor)) {
                $this->messageSuccess('Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('proveedor_index');
    }

    #[Route(path: '/{id}/delete', name: 'proveedor_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, Proveedor $proveedor, ProveedorManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $proveedor);

        if ($this->isCsrfTokenValid('delete_forever'.$proveedor->getId(), $request->request->get('_token'))) {
            if ($manager->remove($proveedor)) {
                $this->messageWarning('Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('proveedor_index');
    }

    #[Route(path: '/new/ajax', name: 'proveedor_new_ajax', methods: ['POST'])]
    public function newAjax(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccess([Permission::NEW]);
        $data = $request->request->all('proveedor');
        $newProveedor = new Proveedor();

        $tipoPersona = $entityManager->getRepository(TipoPersona::class)->find((int) $data['tipoPersona']);
        if (null === $tipoPersona) {
            return $this->json(['status' => false, 'message' => 'Tipo de persona no existe']);
        }
        $newProveedor->setTipoPersona($tipoPersona);

        $tipoDocumento = $entityManager->getRepository(TipoDocumento::class)->find((int) $data['tipoDocumento']);
        if (null === $tipoDocumento) {
            return $this->json(['status' => false, 'message' => 'Tipo de documento no existe']);
        }
        $newProveedor->setTipoDocumento($tipoDocumento);

        $newProveedor->setDireccion($data['direccion']);
        $newProveedor->setTelefono($data['telefono']);

        if ('' === $data['documento']) {
            return $this->json(['status' => false, 'message' => 'Ingrese un numero de documento']);
        }
        $newProveedor->setDocumento($data['documento']);

        if ('' === $data['nombre']) {
            return $this->json(['status' => false, 'message' => 'Ingrese un nombre']);
        }
        $newProveedor->setNombre($data['nombre']);

        $newProveedor->setPropietario($this->getUser());

        $entityManager->persist($newProveedor);
        $entityManager->flush();
        $data = [
            'id' => $newProveedor->getId(),
            'documento' => $newProveedor->getDocumento(),
            'name' => $newProveedor->getNombre(),
        ];

        return $this->json(['status' => true, 'data' => $data]);
    }
}
