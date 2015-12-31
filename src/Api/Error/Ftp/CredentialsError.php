<?php

namespace Undine\Api\Error\Ftp;

use Undine\Api\Error\AbstractError;

/**
 * We captured an FTP error while attempting to install
 */
class CredentialsError extends AbstractError
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
        return 'ftp.credentials_error';
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
