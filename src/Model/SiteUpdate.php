<?php

namespace Undine\Model;

class SiteUpdate
{
    const TYPE_CORE = 'core';

    const TYPE_MODULE = 'module';

    const TYPE_THEME = 'theme';

    const STATUS_NOT_SECURE = 'not_secure';

    const STATUS_REVOKED = 'revoked';

    const STATUS_NOT_SUPPORTED = 'not_supported';

    const STATUS_NOT_CURRENT = 'not_current';

    const STATUS_CURRENT = 'current';

    const STATUS_NOT_CHECKED = 'not_checked';

    const STATUS_UNKNOWN = 'unknown';

    const STATUS_NOT_FETCHED = 'not_fetched';

    const STATUS_FETCH_PENDING = 'fetch_pending';

    private static $statuses = [
        self::STATUS_NOT_SECURE,
        self::STATUS_REVOKED,
        self::STATUS_NOT_SUPPORTED,
        self::STATUS_NOT_CURRENT,
        self::STATUS_CURRENT,
        self::STATUS_NOT_CHECKED,
        self::STATUS_UNKNOWN,
        self::STATUS_NOT_FETCHED,
        self::STATUS_FETCH_PENDING,
    ];

    /**
     * @var Site
     */
    private $site;

    /**
     * @var string
     */
    private $slug;

    /**
     * One of the TYPE_* constants above.
     *
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string|null
     */
    private $project;

    /**
     * @var string|null
     */
    private $package;

    /**
     * @var string
     */
    private $existingVersion;

    /**
     * @var string
     */
    private $recommendedVersion;

    /**
     * @var string
     */
    private $recommendedDownloadLink;

    /**
     * @var string
     */
    private $status;

    /**
     * Array of project slugs. The array always has at least one (current) project.
     *
     * @var string[]
     */
    private $includes = [];

    /**
     * @var bool
     */
    private $enabled;

    /**
     * Array of project slugs.
     *
     * @var string[]
     */
    private $baseThemes = [];

    /**
     * Array of project slugs.
     *
     * @var string[]
     */
    private $subThemes = [];

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
     * @return string[] All valid update statuses.
     */
    public static function getStatuses()
    {
        return self::$statuses;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Site $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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
     * @return string
     */
    public function getExistingVersion()
    {
        return $this->existingVersion;
    }

    /**
     * @param string $existingVersion
     *
     * @return $this
     */
    public function setExistingVersion($existingVersion)
    {
        $this->existingVersion = $existingVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getRecommendedVersion()
    {
        return $this->recommendedVersion;
    }

    /**
     * @param string $recommendedVersion
     *
     * @return $this
     */
    public function setRecommendedVersion($recommendedVersion)
    {
        $this->recommendedVersion = $recommendedVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getRecommendedDownloadLink()
    {
        return $this->recommendedDownloadLink;
    }

    /**
     * @param string $recommendedDownloadLink
     *
     * @return $this
     */
    public function setRecommendedDownloadLink($recommendedDownloadLink)
    {
        $this->recommendedDownloadLink = $recommendedDownloadLink;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    /**
     * @param string[] $includes
     *
     * @return $this
     */
    public function setIncludes($includes)
    {
        $this->includes = $includes;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getBaseThemes()
    {
        return $this->baseThemes;
    }

    /**
     * @param string[] $baseThemes
     *
     * @return $this
     */
    public function setBaseThemes(array $baseThemes)
    {
        $this->baseThemes = $baseThemes;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getSubThemes()
    {
        return $this->subThemes;
    }

    /**
     * @param string[] $subThemes
     *
     * @return $this
     */
    public function setSubThemes(array $subThemes)
    {
        $this->subThemes = $subThemes;

        return $this;
    }
}
