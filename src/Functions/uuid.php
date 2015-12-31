<?php

namespace Undine\Functions;

/**
 * @return string
 */
function generate_uuid1()
{
    return (string)\Ramsey\Uuid\Uuid::uuid1();
}

/**
 * @param mixed $value
 *
 * @return bool
 */
function valid_uuid1($value)
{
    if (!is_string($value)) {
        return false;
    }

    return preg_match('{^([0-9a-f]{4})-([0-9a-f]{4})-([0-9a-f]{8})-([0-9a-f]{4})-([0-9a-f]{12})$}', $value);
}
