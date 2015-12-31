<?php

namespace Undine\Oxygen\Exception\Data;

class ExceptionData
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $class;

    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $context;

    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $line;

    /**
     * @var string
     */
    private $traceString;

    /**
     * @var ExceptionData|null
     */
    private $previous;

    public function __construct($class, $message, $code, $type, $file, $line, $traceString, array $context = array(), ExceptionData $previous = null)
    {
        $this->class       = $class;
        $this->message     = $message;
        $this->code        = $code;
        $this->type        = $type;
        $this->context     = $context;
        $this->file        = $file;
        $this->line        = $line;
        $this->traceString = $traceString;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return string
     */
    public function getTraceString()
    {
        return $this->traceString;
    }

    /**
     * @return ExceptionData|null
     */
    public function getPrevious()
    {
        return $this->previous;
    }
}
