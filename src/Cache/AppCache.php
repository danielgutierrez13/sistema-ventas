<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Cache;

use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use function call_user_func;

final class AppCache
{
    public const CACHE_TIME = 10; //36000 //10 horas
    public const CACHE_TIME_SHORT = 10; //3600;
    public const CACHE_TIME_LONG = 864000; //864000; // 10 dias

    private $appCache;

    public function __construct(TagAwareCacheInterface $appCache)
    {
        $this->appCache = $appCache;
    }

    public function get(string $key, callable $callback, array $tags = [], int $time = self::CACHE_TIME)
    {
        return $this->appCache->get($key, function (ItemInterface $item) use ($callback, $tags, $time) {
            $item->tag($tags);
            $item->expiresAfter($time);

            return call_user_func($callback);
        });
    }

    public function delete(string $key): bool
    {
        try {
            return $this->appCache->delete($key);
        } catch (InvalidArgumentException $e) {
        }

        return false;
    }

    public function deleteTags(array $tags): bool
    {
        try {
            return $this->appCache->invalidateTags($tags);
        } catch (InvalidArgumentException $e) {
        }

        return false;
    }

    public function cache(): CacheInterface
    {
        return $this->appCache;
    }
}
