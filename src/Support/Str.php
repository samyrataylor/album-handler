<?php

namespace SamyraTaylor\AlbumHandler\Support;

class Str
{
    public static function finish(string $string, string $end): string
    {
        if (!self::endsWith($string, $end)) {
            $string .= $end;
        }

        return $string;
    }

    public static function endsWith(string $string, string $end): bool
    {
        return str_ends_with($string, $end);
    }

    public static function start(string $string, string $start): string
    {
        if (!self::startsWith($string, $start)) {
            $string = $start . $string;
        }

        return $string;
    }

    public static function startsWith(string $string, string $start): bool
    {
        return str_starts_with($string, $start);
    }
}