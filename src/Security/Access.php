<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Security;

use Pidia\Apps\Demo\Entity\Usuario;
use function in_array;

final class Access
{
    public const LIST = 'list';
    public const VIEW = 'view';
    public const NEW = 'new';
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    public const PRINT = 'print';
    public const EXPORT = 'export';
    public const IMPORT = 'import';
    public const MASTER = 'master';

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const USER_SUPER_ADMIN = 'admin';

    private $permission;
    private $user;
    private $isSuperAdmin;

    public function __construct(Usuario $usuario, bool $isSuperAdmin = false)
    {
        $this->user = $usuario;
        $this->isSuperAdmin = $isSuperAdmin;
        $this->reset();
    }

    public function ofArray(array $entity, $object = null): self
    {
        if (true === $entity['maestro'] || true === $this->isSuperAdmin()) {
            $this->reset(true);
        } else {
            $hasObject = $object ? $this->isOwner($object) : true;
            $this->permission = [
                self::LIST => $entity['listar'],
                self::VIEW => $entity['mostrar'],
                self::NEW => $entity['crear'],
                self::EDIT => $entity['editar'] && $hasObject,
                self::DELETE => $entity['eliminar'] && $hasObject,
                self::PRINT => $entity['imprimir'],
                self::EXPORT => $entity['exportar'],
                self::IMPORT => $entity['importar'],
                self::MASTER => false,
            ];
        }

        return $this;
    }

    public function has(string $attribute, $object = null): bool
    {
        if (null !== $this->permission) {
            $hasObject = $object ? $this->isOwner($object) : true;
            $maestro = $this->permission[self::MASTER];

            return $maestro || ($this->permission[$attribute] && $hasObject);
        }

        return false;
    }

    public function supports(string $attribute): bool
    {
        if (!in_array(
            $attribute,
            [
                self::LIST,
                self::VIEW,
                self::NEW,
                self::EDIT,
                self::DELETE,
                self::PRINT,
                self::EXPORT,
                self::IMPORT,
                self::MASTER,
            ],
            true
        )) {
            return false;
        }

        return true;
    }

    public function reset(bool $state = false): self
    {
        $this->permission = [
            self::LIST => $state,
            self::VIEW => $state,
            self::NEW => $state,
            self::EDIT => $state,
            self::DELETE => $state,
            self::PRINT => $state,
            self::EXPORT => $state,
            self::IMPORT => $state,
            self::MASTER => $state,
        ];

        return $this;
    }

    private function isOwner($entity): bool
    {
        if (null === $this->user) {
            return false;
        }

//        if (\is_array($entity) && isset($entity['propietario_id'])) {
//            return $entity['propietario_id'] === $this->user->getId();
//        }
//
//        if (\is_object($entity) && null !== ($propietario = $entity->propietario())) {
//            return $this->isConfig($entity) && $propietario->getId() === $this->user->getId();
//        }

        return false;
    }

    public function isConfig($entity): bool
    {
        if (null === $entity) {
            return true;
        }

        if (null === $this->user) {
            return false;
        }

        if (null === ($entityConfig = $entity->config()) || null === ($userConfig = $this->user->config())) {
            return false;
        }

        return $entityConfig->getId() === $userConfig->getId();
    }

    public function isSuperAdmin(): bool
    {
        return $this->isSuperAdmin;
    }

    public function list(): bool
    {
        return $this->has(self::LIST);
    }

    public function view(): bool
    {
        return $this->has(self::VIEW);
    }

    public function new(): bool
    {
        return $this->has(self::NEW);
    }

    public function edit(): bool
    {
        return $this->has(self::EDIT);
    }

    public function delete(): bool
    {
        return $this->has(self::DELETE);
    }

    public function print(): bool
    {
        return $this->has(self::PRINT);
    }

    public function export(): bool
    {
        return $this->has(self::EXPORT);
    }

    public function import(): bool
    {
        return $this->has(self::IMPORT);
    }

    public function master(): bool
    {
        return $this->has(self::MASTER);
    }
}
