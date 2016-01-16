<?php

namespace Undine\Drupal\Exception;

class InstallExtensionException extends ClientException
{
    const FORM_NOT_FOUND = 'form_not_found';

    /**
     * @var string
     */
    private $reason;

    /**
     * @param string $reason
     */
    public function __construct($reason)
    {
        parent::__construct('Extension installation failed: '.$reason);
        $this->reason = $reason;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
}
