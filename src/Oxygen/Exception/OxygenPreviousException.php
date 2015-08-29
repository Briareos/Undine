<?php

namespace Undine\Oxygen\Exception;

/**
 * This exception is used to store data returned from the Oxygen module.
 * It is never directly thrown.
 */
class OxygenPreviousException extends \Exception
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string|null
     */
    private $traceString;

    public function __construct($class, $message, $code, $file = null, $line = null, $traceString = null)
    {
        $this->class       = $class;
        $this->message     = $message;
        $this->code        = $code;
        $this->file        = $file;
        $this->line        = $line;
        $this->traceString = $traceString;
    }

    /**
     * @return string|null
     */
    public function getExceptionClass()
    {
        return $this->class;
    }

    /**
     * We can't override final method getTraceAsString, so just use this method, like in OxygenException.
     *
     * @see OxygenException
     */
    public function getExceptionTraceAsString()
    {
        return $this->traceString;
    }
}
