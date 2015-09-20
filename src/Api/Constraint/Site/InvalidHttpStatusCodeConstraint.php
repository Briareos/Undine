<?php

namespace Undine\Api\Constraint\Site;

use Undine\Api\Constraint\AbstractConstraint;

class InvalidHttpStatusCodeConstraint extends AbstractConstraint
{
    /**
     * @var int
     */
    private $code;

    /**
     * @param int $code
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site.invalid_http_status_code';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'code' => $this->code,
        ];
    }
}
