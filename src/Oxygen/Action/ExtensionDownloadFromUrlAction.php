<?php

namespace Undine\Oxygen\Action;

class ExtensionDownloadFromUrlAction extends AbstractAction
{
    /**
     * @var string
     */
    private $url;

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'extension.downloadFromUrl';
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return [
            'url' => $this->url,
        ];
    }
}
