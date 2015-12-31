<?php

namespace Undine\Loop;

final class ProcessOptions
{
    /**
     * How long to wait (in milliseconds) after sending SIGTERM before sending STOP_SIGNAL define below. Defaults to 10 seconds.
     * Accepts int or float.
     */
    const STOP_TIMEOUT = 'stop_timeout';

    /**
     * A POSIX signal to send in case the process has not stop at timeout. Defaults to SIGKILL.
     */
    const STOP_SIGNAL = 'stop_signal';

    private function __construct()
    {
    }
}
