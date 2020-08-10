<?php

/**
 * @see       https://github.com/sandrokeil/simdjson-php-polyfill for the canonical source repository
 * @copyright https://github.com/sandrokeil/simdjson-php-polyfill/blob/master/COPYRIGHT.md
 * @license   https://github.com/sandrokeil/simdjson-php-polyfill/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Simdjson;

final class Polyfill
{
    public const DEFAULT_DEPTH = 512;
    private const DEFAULT_OPTIONS = \JSON_BIGINT_AS_STRING | \JSON_THROW_ON_ERROR;

    public static function simdjsonDecode(string $json, bool $assoc = false, int $depth = self::DEFAULT_DEPTH)
    {
        return \json_decode($json, $assoc, $depth, self::DEFAULT_OPTIONS);
    }

    public static function simdjsonIsValid(string $json): bool
    {
        try {
            \json_decode($json, false, self::DEFAULT_DEPTH, self::DEFAULT_OPTIONS);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public static function simdjsonKeyValue(
        string $json,
        string $key,
        bool $assoc = false,
        int $depth = self::DEFAULT_DEPTH
    ) {
        $data = self::simdjsonDecode($json, $assoc, $depth);

        if ($assoc === true) {
            $pathValue = self::keyValueArray($key, $data);
        } else {
            $pathValue = self::keyValueObject($key, $data);
        }

        return $pathValue;
    }

    public static function simdjsonKeyExists(string $json, string $key, int $depth = self::DEFAULT_DEPTH): bool
    {
        $data = self::simdjsonDecode($json, true, $depth);

        try {
            self::keyValueArray($key, $data);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function simdjsonKeyCount(string $json, string $key, int $depth = self::DEFAULT_DEPTH): int
    {
        return \count(self::simdjsonKeyValue($json, $key, true, $depth));
    }

    private static function keyValueArray(string $path, array $data)
    {
        $pathKeys = \explode('/', $path);

        foreach ($pathKeys as $pathKey) {
            if (! isset($data[$pathKey])) {
                throw new \Exception(\sprintf('Key "%s" not found for path "%s".', $pathKey, $path));
            }

            $data = $data[$pathKey];
        }

        return $data;
    }

    private static function keyValueObject(string $path, \stdClass $data)
    {
        $pathKeys = \explode('/', $path);

        $copy = $data;

        foreach ($pathKeys as $pathKey) {
            if (\is_array($copy)) {
                if (! isset($copy[$pathKey])) {
                    throw new \Exception(\sprintf('Key "%s" not found for path "%s".', $pathKey, $path));
                }
                $copy = $copy[$pathKey];
            } elseif (! isset($copy->{$pathKey})) {
                throw new \Exception(\sprintf('Key "%s" not found for path "%s".', $pathKey, $path));
            } else {
                $copy = $copy->{$pathKey};
            }
        }

        return $copy;
    }
}
