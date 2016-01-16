<?php

namespace Undine\Thumbnail\Generator;

use Undine\Thumbnail\CaptureConfiguration;

interface GeneratorInterface
{
    /**
     * @param CaptureConfiguration $configuration
     */
    public function generate(CaptureConfiguration $configuration);
}
