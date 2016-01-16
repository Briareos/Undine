<?php

namespace Undine\Api\Error\Api;

use Undine\Api\Error\AbstractError;

class BadRequest extends AbstractError
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $parameter;

    /**
     * @param string      $message
     * @param string|null $parameter
     */
    public function __construct($message, $parameter = null)
    {
        $this->message = $message;
        $this->parameter = $parameter;
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'api.bad_request';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = ['message' => $this->message];
        if ($this->parameter !== null) {
            $data['parameter'] = $this->parameter;
        }

        return $data;
    }
}
