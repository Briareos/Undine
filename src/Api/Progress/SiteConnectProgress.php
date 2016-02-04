<?php

namespace Undine\Api\Progress;

class SiteConnectProgress extends AbstractProgress
{
    const LOG_IN_AS_ADMINISTRATOR = 'log_in_as_administrator';
    const LIST_AVAILABLE_MODULES = 'list_available_modules';
    const INSTALL_OXYGEN_MODULE = 'install_oxygen_module';
    const VERIFY_OXYGEN_MODULE_INSTALLED = 'verify_oxygen_module_installed';
    const ENABLE_OXYGEN_MODULE = 'enable_oxygen_module';
    const DISCONNECT_OXYGEN_MODULE = 'disconnect_oxygen_module';
    const INITIATE_OXYGEN_HANDSHAKE = 'initiate_oxygen_handshake';
    const ENABLE_UPDATE_MODULE = 'enable_update_module';

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
    public function jsonSerialize()
    {
        return [
            'message' => $this->message,
        ];
    }
}
