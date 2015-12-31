<?php

namespace Undine\Drupal\Data;

class ModuleListItem
{
    /**
     * @var string
     */
    private $package;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @param string $package
     * @param string $slug
     * @param bool   $enabled
     */
    public function __construct($package, $slug, $enabled)
    {
        $this->package = $package;
        $this->slug    = $slug;
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}
