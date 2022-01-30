<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\Data\Export\ExportExcel;
use CarlosChininin\Util\Error\Error;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use Pidia\Apps\Demo\Repository\BaseRepository;
use Pidia\Apps\Demo\Util\Paginator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class BaseManager
{
    private array $errors;

    public function __construct(protected EntityManagerInterface $entityManager, protected TokenStorageInterface $tokenStorage)
    {
        $this->errors = [];
    }

    abstract public function repositorio(): BaseRepository|EntityRepository;

    public function manager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function list(array $queryValues, int $page): Paginator
    {
        $params = Paginator::params($queryValues, $page);

        return $this->repositorio()->findLatest($params);
    }

    public function exportOfQuery(array $queryValues, array $headers, string $fileName = 'export'): Response
    {
        $params = Paginator::params($queryValues);
        $items = $this->repositorio()->filter($params, true);

        return $this->export($items, $headers, $fileName);
    }

    public function export(array $items, array $headers, string $fileName = 'export'): Response
    {
        try {
            $export = new ExportExcel($items, $headers);
            $export->execute()->headerStyle()->columnAutoSize();

            return $export->download($fileName);
        } catch (Exception) {
            $this->errors[] = new Error('Exportar fallo!!');
        }

        return new Response('Error');
    }

    public function save(object $entity): bool
    {
        try {
            $entity->updatedDatetime();
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return true;
        } catch (Exception $exception) {
            $this->errors[] = new Error($exception->getMessage());
        }

        return false;
    }

    public function remove(object $entity): bool
    {
        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();

            return true;
        } catch (Exception) {
            $this->errors[] = new Error('La eliminación ha fallado');
        }

        return false;
    }

    public function addError(Error $error): void
    {
        $this->errors[] = $error;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
