<?php

namespace Undine\Api\Command;

use Psr\Http\Message\UriInterface;

class ExtensionDownloadFromUrlCommand extends AbstractCommand
{
    /**
     * @var UriInterface
     */
    private $url;

    /**
     * @param UriInterface $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @return UriInterface
     */
    public function getUrl()
    {
        return $this->url;
    }
}
