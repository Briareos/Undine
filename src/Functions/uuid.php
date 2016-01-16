<?php

namespace Undine\Functions;

use Undine\Functions\Exception\Uuid1TransformException;

/**
 * Generates a UUID that is between v1 and v4 in the way that the first part is timestamp based, and second part is random.
 * It represents itself as v4, which is completely random.
 *
 * [*] It will work until unix timestamp hits 0xfffffffffffff, which is sometimes in 2112.
 * After that just reduce the entropy from left to right, lol.
 *
 * @return string UUID.
 *
 * @throws \Exception After year 2112.
 */
function generate_uuid()
{
    $variants = ['8', '9', 'a', 'b'];
    $time = gettimeofday();
    // 13 characters from left-zero-padded hex value of current micro-time * 1000000.
    $timeHex = dechex((string)$time['sec'].sprintf('%06d', $time['usec']));
    // 18 random characters.
    $randomHex = bin2hex(random_bytes(9));

    if (strlen($timeHex) !== 13 || strlen($randomHex) !== 18) {
        // This should never* happen.
        throw new \Exception('UUID generation failed.');
    }

    // T is time, 4 is UUID version, M is variant (one of 8, 9, A, B), R is random.
    // TTTTTTTT-TTTT-4TRR-MRRR-RRRRRRRRRRRR
    return sprintf(
        '%s-%s-%s-%s-%s',
        substr($timeHex, 0, 8),
        substr($timeHex, 8, 4),
        '4'.substr($timeHex, 12, 1).substr($randomHex, 0, 2),
        $variants[hexdec(substr($randomHex, 2, 1)) % 4].substr($randomHex, 3, 3),
        substr($randomHex, 6, 12)
    );
}

function valid_uuid($value)
{
    if (!is_string($value)) {
        return false;
    }

    return preg_match('{^([0-9a-f]{8})-([0-9a-f]{4})-([0-9a-f]{4})-([0-9a-f]{4})-([0-9a-f]{12})$}', $value);
}

/**
 * @param string $value
 *
 * @return string
 *
 * @throws Uuid1TransformException
 */
function uuid_to_binary($value)
{
    if (!valid_uuid($value)) {
        throw new Uuid1TransformException('Not a valid UUID.');
    }

    return hex2bin(strtr($value, ['-' => '']));
}

/**
 * @param string $value
 *
 * @return string
 *
 * @throws Uuid1TransformException
 */
function binary_to_uuid($value)
{
    if (strlen($value) !== 16) {
        throw new Uuid1TransformException('Not a valid binary representation of a UUID.');
    }

    $unpacked = unpack('H8p1/H4p2/H4p3/H4p4/H12p5', $value);

    return $unpacked['p1'].'-'.$unpacked['p2'].'-'.$unpacked['p3'].'-'.$unpacked['p4'].'-'.$unpacked['p5'];
}
