<?php

namespace Undine\Oxygen\Action;

class ExtensionDownloadUpdateFromUrlAction extends AbstractAction
{
    /**
     * @var string
     */
    private $extension;

    /**
     * @var string
     */
    private $url;

    /**
     * @param string $extension
     * @param string $url
     */
    public function __construct($extension, $url)
    {
        $this->url = $url;
        $this->extension = $extension;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'extension.downloadUpdateFromUrl';
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return [
            'extension' => $this->extension,
            'url' => $this->url,
        ];
    }
}
