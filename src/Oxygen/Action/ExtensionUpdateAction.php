<?php

namespace Undine\Oxygen\Action;

class ExtensionUpdateAction extends AbstractAction
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
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'extension.update';
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return [
            'extension' => $this->extension,
        ];
    }
}
