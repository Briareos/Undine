<?php

namespace Undine\Api\Constraint\Site;

use Undine\Api\Constraint\AbstractConstraint;

class NoResponseConstraint extends AbstractConstraint
{
    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    /**
     * @param int    $code
     * @param string $message The connection error message.
     */
    public function __construct($code, $message)
    {
        $this->code    = $code;
        $this->message = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site.no_response';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'errorCode'    => $this->code,
            'errorMessage' => $this->message,
        ];
    }
}
