<?php

namespace Undine\Oxygen\Action;

use Psr\Http\Message\UriInterface;

class ProjectInstallFromUrlAction extends AbstractAction
{
    /**
     * @var UriInterface
     */
    private $url;

    /**
     * @param UriInterface $url
     */
    public function __construct(UriInterface $url)
    {
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'project.installFromUrl';
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return [
            'url' => (string)$this->url,
        ];
    }
}
