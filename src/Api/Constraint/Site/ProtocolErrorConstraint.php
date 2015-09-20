<?php

namespace Undine\Api\Constraint\Site;

use Undine\Api\Constraint\AbstractConstraint;

class ProtocolErrorConstraint extends AbstractConstraint
{
    /**
     * @var int
     */
    private $code;
    /**
     * @var string
     */
    private $type;

    /**
     * @param int    $code
     * @param string $type
     */
    public function __construct($code, $type)
    {
        $this->code = $code;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site.protocol_error';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'errorCode' => $this->code,
            'errorType' => $this->type,
        ];
    }
}
