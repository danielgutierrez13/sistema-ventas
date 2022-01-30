<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Security;

use Pidia\Apps\Demo\Cache\AppCache;
use Pidia\Apps\Demo\Cache\SecurityCache;
use Pidia\Apps\Demo\Entity\Usuario;
use Pidia\Apps\Demo\Repository\UsuarioPermisoRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use function count;

final class AppSecurity
{
    private $tokenStorage;
    private $repository;
    private $cache;
    private $permisos;
    private $user;
    private $isAuthenticate;
    private $access;

    public function __construct(TokenStorageInterface $tokenStorage, UsuarioPermisoRepository $repository, AppCache $cache)
    {
        $this->tokenStorage = $tokenStorage;
        $this->repository = $repository;
        $this->cache = $cache;
        $this->isAuthenticate = $this->isAuthenticate();
    }

    public function isAuthenticate(): bool
    {
        if (null === $this->isAuthenticate || false === $this->isAuthenticate) {
            $token = $this->tokenStorage->getToken();
            $this->isAuthenticate = (null !== $token) ? $token->isAuthenticated() : false;
        }

        return $this->isAuthenticate;
    }

    private function isRole(string $rol): bool
    {
        if ($this->isAuthenticate() && null !== ($token = $this->tokenStorage->getToken())) {
            $roles = $token->getRoleNames();
            foreach ($roles as $userRol) {
                if ($userRol === $rol) {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasAccess(string $attribute, string $subject, object $object = null): bool
    {
        if (false === $this->isAuthenticate()) {
            return false;
        }

        if (null !== $this->access() && true === $this->isSuperAdmin()) {
            $this->reset(true);

            return true;
        }

//        if (false === $this->access()->isConfig($object)) {
//            $this->reset(false);
//
//            return false;
//        }

        if (false === $this->access()->supports($attribute)) {
            return false;
        }

        $permiso = $this->permisosSubject($subject, $object);

        return $permiso->has($attribute);
    }

    public function denyAccessUnlessGranted(
        string $attribute,
        string $subject,
        object $object = null,
        string $message = 'Acceso denegado...'
    ): void {
        if (!$this->hasAccess($attribute, $subject, $object)) {
            $exception = new AccessDeniedException($message);
            $exception->setAttributes([$attribute]);
            $exception->setSubject($subject);

            throw $exception;
        }
    }

    public function access(): Access
    {
        if (null === $this->access) {
            $isSuperAdmin = $this->isRole('ROLE_SUPER_ADMIN');
            $user = $this->user();
            $this->access = new Access($user, $isSuperAdmin);
        }

        return $this->access;
    }

    public function permisos(): array
    {
        if (null === $this->permisos || 0 === count($this->permisos)) {
            $cacheKey = SecurityCache::permisosPrefix($this->user());
            $this->permisos = $this->cache->get($cacheKey, function () {
                $usuario = $this->user();
                if (null !== $usuario) {
                    return $this->repository->findPermisos($usuario);
                }

                return [];
            });
        }

        return $this->permisos;
    }

    private function permisosSubject(string $subject, ?object $object = null): Access
    {
        $permisos = $this->permisos();
        foreach ($permisos as $permiso) {
            if ($permiso['ruta'] === $subject) {
                $this->access = $this->access()->ofArray($permiso, $object);
                break;
            }
        }

        return $this->access;
    }

    private function reset(bool $state): void
    {
        if (null === $this->access) {
            $this->access = new Access($this->user(), $this->isSuperAdmin());
        }
        $this->access->reset($state);
    }

    public function user(): ?Usuario
    {
        if (null === $this->user) {
            $usuario = (null !== ($token = $this->tokenStorage->getToken())) ? $token->getUser() : null;
            if ($usuario instanceof Usuario) {
                $this->user = $usuario;
            }
        }

        return $this->user;
    }

    public function isSuperAdmin(): bool
    {
        return (null !== ($access = $this->access())) ? $access->isSuperAdmin() : false;
    }
}
