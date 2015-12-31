<?php

namespace Undine\Api\Progress;

class SiteConnectProgress extends AbstractProgress
{
    /**
     * @var string
     */
    private $message;

    /**
     * @param string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * {@inheritdoc}
     */
    function jsonSerialize()
    {
        return [
            'message' => $this->message,
        ];
    }
}
