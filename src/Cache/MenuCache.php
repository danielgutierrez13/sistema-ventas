<?php

declare(strict_types=1);


namespace Pidia\Apps\Demo\Cache;

use function call_user_func;
use CarlosChininin\App\Infrastructure\Cache\BaseCache;
use CarlosChininin\App\Infrastructure\Security\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MenuCache
{
    public const NAME = '__MENUXX__';

    public function __construct(
        private BaseCache $cache,
        private Security $security,
        private TranslatorInterface $translator
    ) {
    }

    public function menus(string $menuSelect, callable $callback)
    {
        $cache_key = $this->keyUser().$menuSelect;

        return $this->cache->get($cache_key, function () use ($callback) {
            return call_user_func($callback);
        }, $this->tags(), BaseCache::CACHE_TIME);
    }

    public function update(): bool
    {
        return $this->cache->deleteTags($this->tags());
    }

    private function keyUser(): string
    {
        return md5(self::NAME.$this->security->user()->getId()).'_'.$this->translator->getLocale().'_';
    }

    private function tags(): array
    {
        return [self::NAME];
    }
}
