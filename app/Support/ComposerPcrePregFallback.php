<?php

namespace Composer\Pcre;

/**
 * Small runtime fallback for shared hosting where Composer's optional pcre
 * package was not deployed. PhpSpreadsheet uses these wrappers only around
 * PHP's built-in PCRE functions.
 */
final class Preg
{
    public static function match(string $pattern, string $subject, ?array &$matches = null, int $flags = 0, int $offset = 0): int
    {
        return self::result(preg_match($pattern, $subject, $matches, $flags | PREG_UNMATCHED_AS_NULL, $offset));
    }

    public static function matchAll(string $pattern, string $subject, ?array &$matches = null, int $flags = 0, int $offset = 0): int
    {
        return self::result(preg_match_all($pattern, $subject, $matches, $flags | PREG_UNMATCHED_AS_NULL, $offset));
    }

    public static function matchAllWithOffsets(string $pattern, string $subject, ?array &$matches, int $flags = 0, int $offset = 0): int
    {
        return self::result(preg_match_all($pattern, $subject, $matches, $flags | PREG_UNMATCHED_AS_NULL | PREG_OFFSET_CAPTURE, $offset));
    }

    public static function replace($pattern, $replacement, $subject, int $limit = -1, ?int &$count = null): string
    {
        return self::stringResult(preg_replace($pattern, $replacement, $subject, $limit, $count));
    }

    public static function replaceCallback($pattern, callable $replacement, $subject, int $limit = -1, ?int &$count = null, int $flags = 0): string
    {
        return self::stringResult(preg_replace_callback($pattern, $replacement, $subject, $limit, $count, $flags | PREG_UNMATCHED_AS_NULL));
    }

    public static function split(string $pattern, string $subject, int $limit = -1, int $flags = 0): array
    {
        $result = preg_split($pattern, $subject, $limit, $flags);
        if ($result === false) {
            throw new \RuntimeException('Invalid regular expression.');
        }

        return $result;
    }

    public static function isMatch(string $pattern, string $subject, ?array &$matches = null, int $flags = 0, int $offset = 0): bool
    {
        return self::match($pattern, $subject, $matches, $flags, $offset) === 1;
    }

    public static function isMatchAll(string $pattern, string $subject, ?array &$matches = null, int $flags = 0, int $offset = 0): bool
    {
        return self::matchAll($pattern, $subject, $matches, $flags, $offset) > 0;
    }

    private static function result(int|false $result): int
    {
        if ($result === false) {
            throw new \RuntimeException('Invalid regular expression.');
        }

        return $result;
    }

    private static function stringResult(string|array|null $result): string
    {
        if (! is_string($result)) {
            throw new \RuntimeException('Invalid regular expression.');
        }

        return $result;
    }
}
