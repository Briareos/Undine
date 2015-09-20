<?php

namespace Undine\Api\Exception;

class RejectedPromiseException extends \Exception
{
    /**
     * @var mixed
     */
    private $reason;

    /**
     * @param mixed $reason
     */
    public function __construct($reason)
    {
        $this->reason = $reason;
        parent::__construct(sprintf('A promise was rejected with a reason of type "%s".', gettype($reason) === 'object' ? get_class($reason) : gettype($reason)));
    }

    /**
     * @return mixed
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param mixed $reason
     *
     * @return \Exception|RejectedPromiseException
     */
    public static function wrap($reason)
    {
        if ($reason instanceof \Exception) {
            return $reason;
        }

        return new self($reason);
    }
}
