<?php

namespace Undine\Api\Command;

class ExtensionUpdateCommand extends AbstractCommand
{
    /**
     * @var string
     */
    private $extension;

    /**
     * @param string $extension
     */
    public function __construct($extension)
    {
        $this->extension = $extension;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }
}
