<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Twig;

use Statickidz\GoogleTranslate;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class TranslationRuntime implements RuntimeExtensionInterface
{
    public const TRANSLATION_CACHE_TAG = 'google_translation_';

    private $translator;
//    private $cache;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
//        $this->cache = $cache;
    }

    public function translate(?string $text, string $source = 'es'): string
    {
        if (null === $text || '' === $text) {
            return '';
        }

        $target = $this->translator->getLocale();
        if ($target === $source) {
            return $text;
        }

        return GoogleTranslate::translate($source, $target, $text);
    }

//    private function inCache(string $text, string $source, string $target): string
//    {
//        return $this->cache->get(self::TRANSLATION_CACHE_TAG.$source.$target.'_'.md5($text), function () use ($source, $target, $text) {
//            return GoogleTranslate::translate($source, $target, $text);
//        }, [self::TRANSLATION_CACHE_TAG], Cache::CACHE_TIME_LONG);
//    }
}
