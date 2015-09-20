<?php

namespace Undine\Api\Constraint\Site;

use Undine\Api\Constraint\AbstractConstraint;

class FtpCredentialsErrorConstraint extends AbstractConstraint
{
    /**
     * @var string|null
     */
    private $ftpError;

    /**
     * @param string|null $error
     */
    public function __construct($error)
    {
        $this->ftpError = $error;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site.ftp_credentials_error';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'ftpError' => $this->ftpError,
        ];
    }
}
