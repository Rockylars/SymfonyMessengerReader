<?php

declare(strict_types=1);

namespace Rocky\SymfonyMessengerReader\Helper;

final class StringHelper
{
    /**
     * When using {@see substr()} on something like 'Café Coffee' with a length of 4, it'll cut the é in half, which
     * leaves the UTF8 as broken. That is why this function should be used instead.
     */
    public static function substrEncodingAware(string $string, int $offset, ?int $length = null): string
    {
        return mb_substr($string, $offset, $length, 'UTF-8');
    }

    /**
     * Returns the base name of a reference object (class) without Reflection as that is slower.
     * @param object|string $class
     */
    public static function shortClassName($class): string
    {
        // return (new \ReflectionClass($class))->getShortName();
        return basename(str_replace('\\', DIRECTORY_SEPARATOR, is_string($class) ? $class : get_class($class)));
    }

    /**
     * Replaces the later get_debug_type() with a sensible alternative.
     * @param mixed $mixed
     */
    public static function getDebugType($mixed): string
    {
        return is_object($mixed) ? get_class($mixed) : gettype($mixed);
    }
}
