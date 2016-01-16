<?php

namespace Undine\Functions;

/**
 * Shorthand method for json_decode() that casts (string) on the first argument,
 * throws an exception on invalid JSON, and returns an associative array by default.
 *
 * @param string|object $json String or an object that implements __toString().
 * @param bool          $assoc
 * @param int           $depth
 * @param int           $options
 *
 * @return mixed
 *
 * @throws Exception\JsonParseException
 */
function json_parse($json, $assoc = true, $depth = 512, $options = 0)
{
    $data = json_decode((string)$json, $assoc, $depth, $options);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception\JsonParseException(sprintf('Unable to parse JSON string; error code [%s]: %s', json_last_error(), json_last_error_msg()));
    }

    return $data;
}
