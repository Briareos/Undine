<?php

namespace Undine\Api\Error;

interface ErrorInterface
{
    /**
     * Error name. Should be in format error_group.error_name.
     *
     * @return string
     */
    public static function getName();

    /**
     * Any additional constraint info that should be passed in the API response.
     * This data must not contain sensitive information and it should not contain objects.
     * Try to keep it simple, only pass info that would give some context to the error.
     * Eg. if a timeout happens, pass in the active timeout value, but DO NOT pass the
     * actual passed time (in float) because it could be used in a timing-attacks.
     *
     * @link https://en.wikipedia.org/wiki/Timing_attack
     *
     * @return array
     */
    public function getData();

    /**
     * @return string
     */
    public function __toString();
}
