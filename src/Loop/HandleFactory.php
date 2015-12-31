<?php

namespace Undine\Loop;

use Psr\Http\Message\RequestInterface;
use Symfony\Component\Process\Process;

class HandleFactory
{
    /**
     * @param RequestInterface|Process|callable $request
     * @param array $options
     *
     * @return LoopHandle
     */
    public function create($request, array $options)
    {
        return new LoopHandle($request, $options);
    }
}
