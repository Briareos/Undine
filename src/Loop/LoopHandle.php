<?php

namespace Undine\Loop;

use Symfony\Component\Process\Process;

class LoopHandle
{
    /**
     * @var resource|Process|callable
     */
    public $handle;

    /**
     * @var array
     */
    public $options;

    public function __construct($handle, array $options)
    {
        $this->handle = $handle;
        $this->options = $options;
    }
}
