<?php

/**
 * @see       https://github.com/sandrokeil/simdjson-php-polyfill for the canonical source repository
 * @copyright https://github.com/sandrokeil/simdjson-php-polyfill/blob/master/COPYRIGHT.md
 * @license   https://github.com/sandrokeil/simdjson-php-polyfill/blob/master/LICENSE.md MIT License
 */

use Simdjson\Polyfill as p;

if (!function_exists('simdjson_is_valid')) {
    function simdjson_is_valid(string $json)
    {
        return p::simdjsonIsValid($json);
    }
}
if (!function_exists('simdjson_key_value')) {
    function simdjson_key_value(string $json, bool $assoc = false, int $depth = p::DEFAULT_DEPTH)
    {
        return p::simdjsonKeyValue($json, $assoc, $depth);
    }
}
if (!function_exists('simdjson_key_count')) {
    function simdjson_key_count(string $json, bool $assoc = false, int $depth = p::DEFAULT_DEPTH)
    {
        return p::simdjsonKeyCount($json, $assoc, $depth);
    }
}
if (!function_exists('simdjson_key_exists')) {
    function simdjson_key_exists(string $json, string $key, int $depth = p::DEFAULT_DEPTH)
    {
        return p::simdjsonKeyExists($json, $key, $depth);
    }
}
if (!function_exists('simdjson_decode')) {
    function simdjson_decode(string $json, bool $assoc = false, int $depth = p::DEFAULT_DEPTH)
    {
        return p::simdjsonDecode($json, $assoc, $depth);
    }
}
