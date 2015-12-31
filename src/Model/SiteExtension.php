<?php

namespace Undine\Model;

use Ramsey\Uuid\Uuid;

class SiteExtension
{
    const TYPE_MODULE = 'module';

    const TYPE_THEME = 'theme';

    /**
     * @var string
     */
    private $id;

    /**
     * @var SiteState
     */
    private $siteState;

    /**
     * @var string
     */
    private $filename;

    /**
     * One of the TYPE_* constants above.
     *
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string|null
     */
    private $parent;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string|null
     */
    private $package;

    /**
     * @var string|null
     */
    private $version;

    /**
     * @var bool
     */
    private $required;

    /**
     * @var string[]
     */
    private $dependencies = [];

    /**
     * @var string|null
     */
    private $project;

    /**
     * @param SiteState $siteState
     * @param string    $slug
     */
    public function __construct(SiteState $siteState, $slug)
    {
        $this->id        = \Undine\Functions\generate_uuid1();
        $this->siteState = $siteState;
        $this->siteState = $siteState;
        $this->slug      = $slug;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Site
     */
    public function getSiteState()
    {
        return $this->siteState;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param string|null $parent
     *
     * @return $this
     */
    public function setParent($parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param string|null $package
     *
     * @return $this
     */
    public function setPackage($package = null)
    {
        $this->package = $package;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string|null $version
     *
     * @return $this
     */
    public function setVersion($version = null)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     *
     * @return $this
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @param string[] $dependencies
     *
     * @return $this
     */
    public function setDependencies(array $dependencies)
    {
        $this->dependencies = $dependencies;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param string|null $project
     *
     * @return $this
     */
    public function setProject($project = null)
    {
        $this->project = $project;

        return $this;
    }
}
