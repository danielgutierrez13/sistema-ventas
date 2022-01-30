<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Security;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use Pidia\Apps\Demo\Cache\AppCache;
use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Entity\Usuario;
use Pidia\Apps\Demo\Entity\UsuarioPermiso;
use Pidia\Apps\Demo\Util\Generator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use function count;

final class Security
{
    public const TAGS_CACHE = ['security'];
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const ROLE_ADMIN = 'ROLE_ADMIN_SYSTEM';
    public const LIST = 'list';
    public const VIEW = 'view';
    public const NEW = 'new';
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    public const PRINT = 'print';
    public const EXPORT = 'export';
    public const IMPORT = 'import';
    public const MASTER = 'master';

    private array $user = [];
    private array $access = [];
    private ?bool $isAuthenticate = null;
    private ?bool $isSuperAdmin = null;
    private ?bool $isAdmin = null;
    private ?bool $useCache;
    private ?string $subject;

    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private EntityManagerInterface $entityManager,
        private AppCache $appCache
    ) {
        $this->cacheable(false);
        $this->user();
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

    public function isAuthenticate(): bool
    {
        if (null === $this->isAuthenticate) {
            $this->isAuthenticate = (bool) $this->tokenStorage?->getToken()?->getUser();
        }

        return $this->isAuthenticate;
    }

    public function user(): array
    {
        if (0 === count($this->user)) {
            if ($this->useCache) {
                $key = 'security_user_'.$this->keyUserCache();
                $this->user = $this->appCache->get($key, function () {
                    return $this->getUser();
                }, self::TAGS_CACHE, AppCache::CACHE_TIME_SHORT);

                if (0 === count($this->user)) {
                    $this->deleteCache();
                }
            } else {
                $this->user = $this->getUser();
            }
        }

        return $this->user;
    }

    private function getUser(): array
    {
        $usuario = (null !== ($token = $this->tokenStorage->getToken())) ? $token->getUser() : null;
        if ($usuario instanceof Usuario) {
            return $this->entityManager->getRepository(Usuario::class)->findValuesArrayByUsuarioId($usuario->getId());
        }

        return [];
    }

    public function hasAccess(string $attribute, string $subject, object $object = null): bool
    {
        $this->subject = $subject;

        return $this->has($attribute, $object);
    }

    public function has(string $attribute, ?object $object = null, ?string $subject = null): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $subject = $subject ?? $this->subject;

        if ($this->isAdmin()) {
            return $this->hasAccessAdmin($subject);
        }

        $access = $this->access();

        if (null === $subject || !isset($access[$subject])) {
            return false;
        }

        return $access[$subject][self::MASTER] || ($access[$subject][$attribute] && $this->owner($object));
    }

    public function owner(?object $object): bool
    {
        if (null === $object || null === $object->config()) {
            return true;
        }

        $propietarioId = $object->propietario() ? $object->propietario()->getId() : 0;
        $usuario = $this->user();

        return $propietarioId === $usuario['id'];
    }

    private function access(): array
    {
        if ($this->isSuperAdmin()) {
            return [];
        }

        if (0 !== count($this->access)) {
            return $this->access;
        }

        if ($this->useCache) {
            $key = 'security_access_'.$this->keyUserCache();
            $permisssions = $this->appCache->get($key, function () {
                return $this->getPermissions();
            }, self::TAGS_CACHE, AppCache::CACHE_TIME_SHORT);

            if (0 === count($permisssions)) {
                $this->deleteCache();
            }
        } else {
            $permisssions = $this->getPermissions();
        }

        $this->access = [];
        foreach ($permisssions as $permission) {
            $this->access[$permission['route']] = $permission;
        }

        return $this->access;
    }

    private function getPermissions(): array
    {
        $usuario = $this->user();
        if (isset($usuario['id'])) {
            return $this->entityManager->getRepository(UsuarioPermiso::class)
                ->findPermisosByUsuarioIdAndRuta($usuario['id']);
        }

        return [];
    }

    public function isSuperAdmin(): bool
    {
        if (null === $this->isSuperAdmin) {
            $this->isSuperAdmin = $this->isGranted(self::ROLE_SUPER_ADMIN);
        }

        return $this->isSuperAdmin;
    }

    public function isAdmin(): bool
    {
        if (null === $this->isAdmin) {
            $this->isAdmin = $this->isGranted(self::ROLE_ADMIN);
        }

        return $this->isAdmin;
    }

    public function isGranted(string $role): bool
    {
        $usuario = $this->user();
        if (0 === count($usuario)) {
            return false;
        }

        foreach ($usuario['usuarioRoles'] as $rol) {
            if ($role === $rol['rol']) {
                return true;
            }
        }

        return false;
    }

    public function config(): array
    {
        $usuario = $this->user();

        return $usuario['config'] ?? [];
    }

    public function configQuery(QueryBuilder $queryBuilder, bool $forced = false): void
    {
        if ($this->isSuperAdmin() && true !== $forced) {
            return;
        }

        $config = $this->config();
        if (!isset($config['id'])) {
            return;
        }

        $queryBuilder
            ->andWhere('config.id = :config_id')
            ->setParameter('config_id', $config['id'])
        ;
    }

    public function cacheable(bool $useCache = true): void
    {
        $this->useCache = $useCache;
    }

    public function deleteCache(): bool
    {
        return $this->appCache->deleteTags(self::TAGS_CACHE);
    }

    private function keyUserCache(): string
    {
        $key = 'no-user';
        $usuario = $this->tokenStorage?->getToken()?->getUser();
        if (null !== $usuario) {
            $key = $usuario->getUserIdentifier();
        }

        return Generator::encryptKeyCache($key);
    }

    public function keyCache(bool $encrypt = true): string
    {
        $key = $this->keyCacheUser().'-'.$this->keyCacheConfig();

        return $encrypt ? Generator::encryptKeyCache($key) : $key;
    }

    private function keyCacheUser(): string
    {
        $user = $this->user();

        return isset($user['uuid']) ? (string) $user['uuid'] : 'no-user';
    }

    private function keyCacheConfig(): string
    {
        $config = $this->config();

        return isset($config['uuid']) ? (string) $config['uuid'] : 'no-config';
    }

    public function tagConfig(): string
    {
        return Generator::encryptKeyCache($this->keyCacheConfig());
    }

    public function entityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function repository(string $className): ObjectRepository
    {
        return $this->entityManager()->getRepository($className);
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

    private function hasAccessAdmin(?string $subject): bool
    {
        $config = $this->config();
        $menus = $this->entityManager->getRepository(Config::class)->findMenusByConfigId($config['id']);
        foreach ($menus as $menu) {
            if ($menu['route'] === $subject) {
                return true;
            }
        }

        return false;
    }
}
