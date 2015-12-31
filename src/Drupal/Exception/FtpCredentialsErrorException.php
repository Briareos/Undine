<?php

namespace Undine\Drupal\Exception;

class FtpCredentialsErrorException extends ClientException
{
    /**
     * @var string|null
     */
    private $clientMessage;

    /**
     * @param string $clientMessage
     */
    public function __construct($clientMessage)
    {
        parent::__construct(sprintf('The submitted FTP credentials form returned with an error: %s.', empty($clientMessage) ? '(empty)' : $clientMessage));
        $this->clientMessage = $clientMessage;
    }

    /**
     * @return string|null
     */
    public function getClientMessage()
    {
        return $this->clientMessage;
    }
}
