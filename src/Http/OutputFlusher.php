<?php

namespace Undine\Http;

class OutputFlusher
{
    /**
     * @var int
     */
    private $padLength;

    const PAD_CHARACTER = ' ';

    /**
     * Internal tracker of the buffer status.
     *
     * @var bool
     */
    private $bufferingDisabled = false;

    /**
     * @param int $padLength
     */
    public function __construct($padLength)
    {
        $this->padLength = $padLength;
    }

    public function flushMessage($message)
    {
        if (!is_scalar($message)) {
            throw new \InvalidArgumentException('$message must be a scalar type.');
        }

        if (!$this->bufferingDisabled) {
            ob_implicit_flush(true);
            $this->bufferingDisabled = true;
        }

        $output = str_pad($message, $this->padLength, self::PAD_CHARACTER);
        echo $output, "\n";
        @ob_flush();
    }
}
