<?php

namespace Undine\Model;

class SiteExtension
{
    const TYPE_MODULE = 'module';

    const TYPE_THEME = 'theme';

    const TYPE_PROFILE = 'profile';

    const TYPE_THEME_EXTENSION = 'theme_extension';

    /**
     * @var Site
     */
    private $site;

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
    private $status;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $package;

    /**
     * @var string
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
     * @param Site   $site
     * @param string $slug
     */
    public function __construct(Site $site, $slug)
    {
        $this->site = $site;
        $this->slug = $slug;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
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
     * @return boolean
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

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
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param string $package
     *
     * @return $this
     */
    public function setPackage($package)
    {
        $this->package = $package;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $required
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
