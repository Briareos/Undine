<?php

namespace Undine\Api\Error\Oxygen;

use Undine\Api\Error\AbstractError;

/**
 * An exception was thrown by the Oxygen module.
 */
class ErrorConstraint extends AbstractError
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $file;

    /**
     * @var int
     */
    private $line;

    /**
     * @var string
     */
    private $trace;

    /**
     * @param string $message
     * @param int    $code
     * @param string $type
     * @param string $file
     * @param int    $line
     * @param string $trace
     */
    public function __construct($message, $code, $type, $file, $line, $trace)
    {
        $this->message = $message;
        $this->code    = $code;
        $this->type    = $type;
        $this->file    = $file;
        $this->line    = $line;
        $this->trace   = $trace;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'oxygen.exception';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'message' => $this->message,
            'code'    => $this->code,
            'type'    => $this->type,
            'file'    => $this->file,
            'line'    => $this->line,
            'trace'   => $this->trace,
        ];
    }
}
