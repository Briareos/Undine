<?php

namespace Undine\Oxygen\Exception;

abstract class ProtocolException extends \Exception
{
    const LEVEL_NETWORK = 'network';

    const LEVEL_RESPONSE = 'response';

    const LEVEL_OXYGEN = 'oxygen';

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

    /**
     * @return array
     */
    public function getContext()
    {
        return [];
    }
}
