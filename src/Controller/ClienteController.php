<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Pidia\Apps\Demo\Entity\Cliente;
use Pidia\Apps\Demo\Entity\TipoDocumento;
use Pidia\Apps\Demo\Entity\TipoPersona;
use Pidia\Apps\Demo\Form\ClienteType;
use Pidia\Apps\Demo\Manager\BusquedaApiManager;
use Pidia\Apps\Demo\Manager\ClienteManager;
use Pidia\Apps\Demo\Repository\TipoDocumentoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/cliente')]
class ClienteController extends WebAuthController
{
    public const BASE_ROUTE = 'cliente_index';

    #[Route(path: '/', name: 'cliente_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'cliente_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, ClienteManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'cliente/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'cliente_export', methods: ['GET'])]
    public function export(Request $request, ClienteManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'tipoPersona.descripcion' => 'Tipo de Persona',
            'nombre' => 'Nombre de Cliente',
            'tipoDocumento.descripcion' => 'Tipo de Documento',
            'documento' => 'N° de Documento',
            'direccion' => 'Direccion',
            'telefono' => 'Telefono',
            'activo' => 'Activo',
        ];

        $items = $manager->dataExport(ParamFetcher::fromRequestQuery($request), true);

        return $manager->export($items, $headers, 'cliente');
    }

    #[Route(path: '/new', name: 'cliente_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ClienteManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);
        $cliente = new Cliente();
        $form = $this->createForm(ClienteType::class, $cliente);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($cliente)) {
                $this->messageSuccess('Registro creado!!!');
            }
            return $this->redirectToRoute('cliente_index');
        }

        return $this->renderForm(
            'cliente/new.html.twig',
            [
                'cliente' => $cliente,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'cliente_show', methods: ['GET'])]
    public function show(Cliente $cliente): Response
    {
        $this->denyAccess([Permission::SHOW], $cliente);

        return $this->render('cliente/show.html.twig', ['cliente' => $cliente]);
    }

    #[Route(path: '/{id}/edit', name: 'cliente_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cliente $cliente, ClienteManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $cliente);

        $form = $this->createForm(ClienteType::class, $cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($cliente)) {
                $this->messageSuccess('Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('cliente_index', ['id' => $cliente->getId()]);
        }

        return $this->render(
            'cliente/edit.html.twig',
            [
                'cliente' => $cliente,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'cliente_delete', methods: ['POST'])]
    public function state(Request $request, Cliente $cliente, ClienteManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $cliente);

        if ($this->isCsrfTokenValid('delete'.$cliente->getId(), $request->request->get('_token'))) {
            $cliente->changeActivo();
            if ($manager->save($cliente)) {
                $this->messageSuccess('Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('cliente_index');
    }

    #[Route(path: '/{id}/delete', name: 'cliente_delete_forever', methods: ['POST'])]
    public function deleteForever(Request $request, Cliente $cliente, ClienteManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $cliente);

        if ($this->isCsrfTokenValid('delete_forever'.$cliente->getId(), $request->request->get('_token'))) {
            if ($manager->remove($cliente)) {
                $this->messageWarning('Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('cliente_index');
    }

    #[Route('/busqueda/dni', name: 'cliente_busqueda_api_dni')]
    public function busquedaPorDni(Request $request, BusquedaApiManager $apiManager): Response
    {
        $dni = $request->request->get('dni');

        $result = $apiManager->dni($dni);

        return $this->json($result);
    }

    #[Route('/busqueda/ruc', name: 'cliente_busqueda_api_ruc')]
    public function busquedaPorRuc(Request $request, BusquedaApiManager $apiManager): Response
    {
        $ruc = $request->request->get('ruc');

        $result = $apiManager->ruc($ruc);

        return $this->json($result);
    }

    #[Route('/busqueda/tipo_documento', name: 'documento_for_tipo_persona')]
    public function documentoForTipo(Request $request, TipoDocumentoRepository $documentoRepository): Response
    {
        $idTipoPersona = $request->request->get('idTipoPersona');
        $array = $documentoRepository->documentoForTipoPersona($idTipoPersona);
        if (null === $array) {
            return $this->json(['status' => false, 'message' => 'No se encontro el producto']);
        }

        return $this->json(['status' => true, 'data' => $array]);
    }

    #[Route(path: '/new/ajax', name: 'cliente_new_ajax', methods: ['POST'])]
    public function newAjax(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccess([Permission::NEW]);
        $data = $request->request->all('cliente');
        $newCliente = new Cliente();

        $tipoPersona = $entityManager->getRepository(TipoPersona::class)->find((int) $data['tipoPersona']);
        if (null === $tipoPersona) {
            return $this->json(['status' => false, 'message' => 'Tipo de persona no existe']);
        }
        $newCliente->setTipoPersona($tipoPersona);

        $tipoDocumento = $entityManager->getRepository(TipoDocumento::class)->find((int) $data['tipoDocumento']);
        if (null === $tipoDocumento) {
            return $this->json(['status' => false, 'message' => 'Tipo de documento no existe']);
        }
        $newCliente->setTipoDocumento($tipoDocumento);

        $newCliente->setDireccion($data['direccion']);
        $newCliente->setTelefono($data['telefono']);

        if ('' === $data['documento']) {
            return $this->json(['status' => false, 'message' => 'Ingrese un numero de documento']);
        }
        $newCliente->setDocumento($data['documento']);

        if ('' === $data['nombre']) {
            return $this->json(['status' => false, 'message' => 'Ingrese un nombre']);
        }
        $newCliente->setNombre($data['nombre']);

        $newCliente->setPropietario($this->getUser());

        $entityManager->persist($newCliente);
        $entityManager->flush();

        $data = [
            'id' => $newCliente->getId(),
            'name' => $newCliente->getNombre(),
        ];

        return $this->json(['status' => true, 'data' => $data]);
    }
}
