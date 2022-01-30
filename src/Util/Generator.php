<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Util;

use const STR_PAD_LEFT;

class Generator
{
    public static function code(int $length = 6): string
    {
        $key = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
        $max = mb_strlen($pattern) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $key .= $pattern[mt_rand(0, $max)];
        }

        return $key;
    }

    public static function slugify(string $string): string
    {
        return preg_replace('/\s+/', '-', mb_strtolower(trim(strip_tags($string)), 'UTF-8'));
    }

    public static function join(?string $pre, ?string $num, int $preCount = 4, int $numCount = 7): ?string
    {
        if (null === $pre || null === $num) {
            return null;
        }

        return str_pad($pre, $preCount, '0', STR_PAD_LEFT).'-'.str_pad($num, $numCount, '0', STR_PAD_LEFT);
    }

    public static function splitAndJoin(string $number, int $preCount = 4, int $numCount = 7): ?string
    {
        $divide = explode('-', $number);
        if (2 === \count($divide)) {
            $serie = (int) $divide[0];
            $correlativo = (int) $divide[1];

            return str_pad($serie, $preCount, '0', STR_PAD_LEFT).'-'.str_pad($correlativo, $numCount, '0', STR_PAD_LEFT);
        }

        return null;
    }

    public static function serialNumber($number, int $numCount = 7): ?string
    {
        if (null === $number) {
            return null;
        }

        return str_pad($number, $numCount, '0', STR_PAD_LEFT);
    }

    public static function split(string $num, int $position = 1): ?string
    {
        $divide = explode('-', $num);
        if ($position >= 0 && $position < \count($divide)) {
            return $divide[$position];
        }

        return null;
    }

    public static function splitNumeroDocumento(string $numero): array
    {
        $divide = explode('-', $numero);
        if (2 === \count($divide)) {
            return $divide;
        }

        return [];
    }

    public static function withoutWhiteSpaces(?string $text): ?string
    {
        if (null === $text) {
            return null;
        }

        return str_replace(' ', '', $text);
    }

    public static function createRol(string $rolName, int $configId): string
    {
        return 'ROLE_'.preg_replace('/\s+/', '_', mb_strtoupper(trim(strip_tags($rolName)), 'UTF-8')).'_'.$configId;
    }

    public static function encryptKeyCache(string $key): string
    {
        return md5($key);
    }
}
