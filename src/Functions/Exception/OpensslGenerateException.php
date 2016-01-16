<?php

namespace Undine\Functions\Exception;

class OpensslGenerateException extends \Exception
{
    /**
     * @param string $message The error returned from openssl_error_string().
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
