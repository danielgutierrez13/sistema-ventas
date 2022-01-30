<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Cache;

use Pidia\Apps\Demo\Security\Security;
use function call_user_func;

final class MenuCache
{
    private $cache;
    private $security;

    public function __construct(AppCache $cache, Security $security)
    {
        $this->cache = $cache;
        $this->security = $security;
    }

    public function permisos(callable $callback)
    {
        $cache_key = $this->prefix().$this->permisosKey();
        $cache_tags = $this->tags();

        return $this->cache->get($cache_key, function () use ($callback) {
            return call_user_func($callback);
        }, $cache_tags, AppCache::CACHE_TIME_SHORT);
    }

    public function menus(callable $callback)
    {
        $cache_key = $this->prefix().$this->menusKey();
        $cache_tags = $this->tags();

        return $this->cache->get($cache_key, function () use ($callback) {
            return call_user_func($callback);
        }, $cache_tags, AppCache::CACHE_TIME_SHORT);
    }

    public function update(): bool
    {
        $cache_tags = [$this->prefix()];

        return $this->cache->deleteTags($cache_tags);
    }

    public function permisosKey(): string
    {
        return 'permisos_';
    }

    public function menusKey(): string
    {
        return 'menus_';
    }

    private function prefix(): string
    {
        return $this->security->keyCache().'_MENU__';
    }

    private function tags(): array
    {
        return [$this->prefix(), $this->security->tagConfig()];
    }
}
