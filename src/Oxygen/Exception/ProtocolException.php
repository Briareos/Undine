<?php

namespace Undine\Oxygen\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Undine\Oxygen\Exception\Data\TransferInfo;

abstract class ProtocolException extends \Exception
{
    const LEVEL_NETWORK = 'network';

    const LEVEL_RESPONSE = 'response';

    const LEVEL_OXYGEN = 'oxygen';

    /**
     * @return string One of the LEVEL_* constants above.
     */
    abstract public function getLevel();

    /**
     * @return string Error code transformed into a simple string (constant).
     */
    abstract public function getType();
}
