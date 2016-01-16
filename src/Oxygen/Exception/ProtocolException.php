<?php

namespace Undine\Oxygen\Exception;

abstract class ProtocolException extends \Exception
{
    const LEVEL_NETWORK = 'NETWORK';

    const LEVEL_RESPONSE = 'RESPONSE';

    const LEVEL_OXYGEN = 'OXYGEN';

    /**
     * @return string One of the LEVEL_* constants of this class.
     */
    abstract public function getLevel();

    /**
     * @return string Error code transformed into a simple string (constant).
     */
    abstract public function getType();

    /**
     * Convenience method to check for all exception cases.
     *
     * @param int $code
     *
     * @return bool
     */
    public function is($code)
    {
        return $this->code === $code;
    }
}
