<?php

namespace Undine\Model\Site;

class SiteState
{
    const DATABASE_DRIVER_MYSQL = 'mysql';

    const DATABASE_DRIVER_PGSQL = 'pgsql';

    const DATABASE_DRIVER_SQLITE = 'sqlite';

    /**
     * @see PHP_VERSION
     *
     * @var string|null
     */
    private $phpVersion;

    /**
     * @see PHP_VERSION_ID
     *
     * @var int|null
     */
    private $phpVersionId;

    /**
     * One of 'mysql', 'pgsql', 'sqlite'.
     *
     * @var string|null
     */
    private $databaseDriver;

    /**
     * @var string|null
     */
    private $databaseDriverVersion;

    /**
     * @var string|null
     */
    private $databaseTablePrefix;

    /**
     * PHP memory limit in bytes.
     *
     * @var int|null
     */
    private $memoryLimit;

    /**
     * Either 32 or 64.
     *
     * @var int|null
     */
    private $processArchitecture;

    /**
     * @var string|null
     */
    private $internalIp;

    /**
     * @var string|null
     */
    private $uname;

    /**
     * @var string|null
     */
    private $hostname;

    /**
     * @see PHP_OS
     *
     * @var string|null
     */
    private $os;

    /**
     * @var bool|null
     */
    private $windows;

    /**
     * @var string|null
     */
    private $cronKey;

    /**
     * @var \DateTime|null
     */
    private $cronLastRunAt;

    /**
     * @var string|null
     */
    private $siteName;

    /**
     * @var string|null
     */
    private $siteMail;

    /**
     * Drupal's internal "site_key", used for statistic tracking.
     *
     * @var string|null
     */
    private $siteKey;

    /**
     * In most cases the same as $drupalRoot, but let's play it safe and keep both.
     *
     * @var string|null
     */
    private $siteRoot;

    /**
     * @var string|null
     */
    private $drupalRoot;

    /**
     * @var string|null
     */
    private $drupalVersion;

    /**
     * @var int|null
     */
    private $drupalMajorVersion;

    /**
     * @var \DateTime|null
     */
    private $updateLastCheckAt;

    /**
     * @var \DateTimeZone|null
     */
    private $timezone;

    /**
     * @var string|null
     */
    private $extensionsChecksum;

    /**
     * @return string|null
     */
    public function getPhpVersion()
    {
        return $this->phpVersion;
    }

    /**
     * @param string|null $phpVersion
     *
     * @return $this
     */
    public function setPhpVersion($phpVersion = null)
    {
        $this->phpVersion = $phpVersion;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPhpVersionId()
    {
        return $this->phpVersionId;
    }

    /**
     * @param int|null $phpVersionId
     *
     * @return $this;
     */
    public function setPhpVersionId($phpVersionId = null)
    {
        $this->phpVersionId = $phpVersionId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDatabaseDriver()
    {
        return $this->databaseDriver;
    }

    /**
     * @param string|null $databaseDriver
     *
     * @return $this
     */
    public function setDatabaseDriver($databaseDriver = null)
    {
        $this->databaseDriver = $databaseDriver;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDatabaseDriverVersion()
    {
        return $this->databaseDriverVersion;
    }

    /**
     * @param string|null $databaseDriverVersion
     *
     * @return $this
     */
    public function setDatabaseDriverVersion($databaseDriverVersion = null)
    {
        $this->databaseDriverVersion = $databaseDriverVersion;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDatabaseTablePrefix()
    {
        return $this->databaseTablePrefix;
    }

    /**
     * @param string|null $databaseTablePrefix
     *
     * @return $this
     */
    public function setDatabaseTablePrefix($databaseTablePrefix = null)
    {
        $this->databaseTablePrefix = $databaseTablePrefix;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMemoryLimit()
    {
        return $this->memoryLimit;
    }

    /**
     * @param int|null $memoryLimit
     *
     * @return $this
     */
    public function setMemoryLimit($memoryLimit = null)
    {
        $this->memoryLimit = $memoryLimit;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getProcessArchitecture()
    {
        return $this->processArchitecture;
    }

    /**
     * @param int|null $processArchitecture
     *
     * @return $this
     */
    public function setProcessArchitecture($processArchitecture = null)
    {
        $this->processArchitecture = $processArchitecture;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInternalIp()
    {
        return $this->internalIp;
    }

    /**
     * @param string|null $internalIp
     *
     * @return $this
     */
    public function setInternalIp($internalIp = null)
    {
        $this->internalIp = $internalIp;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUname()
    {
        return $this->uname;
    }

    /**
     * @param string|null $uname
     *
     * @return $this
     */
    public function setUname($uname = null)
    {
        $this->uname = $uname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param string|null $hostname
     *
     * @return $this
     */
    public function setHostname($hostname = null)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * @param string|null $os
     *
     * @return $this
     */
    public function setOs($os = null)
    {
        $this->os = $os;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isWindows()
    {
        return $this->windows;
    }

    /**
     * @param bool|null $windows
     *
     * @return $this
     */
    public function setWindows($windows = null)
    {
        $this->windows = $windows;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCronKey()
    {
        return $this->cronKey;
    }

    /**
     * @param string|null $cronKey
     *
     * @return $this
     */
    public function setCronKey($cronKey = null)
    {
        $this->cronKey = $cronKey;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCronLastRunAt()
    {
        return $this->cronLastRunAt;
    }

    /**
     * @param \DateTime|null $cronLastRunAt
     *
     * @return $this
     */
    public function setCronLastRunAt(\DateTime $cronLastRunAt = null)
    {
        $this->cronLastRunAt = $cronLastRunAt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * @param string|null $siteName
     *
     * @return $this
     */
    public function setSiteName($siteName = null)
    {
        $this->siteName = $siteName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSiteMail()
    {
        return $this->siteMail;
    }

    /**
     * @param string|null $siteMail
     *
     * @return $this
     */
    public function setSiteMail($siteMail = null)
    {
        $this->siteMail = $siteMail;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSiteKey()
    {
        return $this->siteKey;
    }

    /**
     * @param string|null $siteKey
     *
     * @return $this
     */
    public function setSiteKey($siteKey = null)
    {
        $this->siteKey = $siteKey;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSiteRoot()
    {
        return $this->siteRoot;
    }

    /**
     * @param string|null $siteRoot
     *
     * @return $this
     */
    public function setSiteRoot($siteRoot = null)
    {
        $this->siteRoot = $siteRoot;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDrupalRoot()
    {
        return $this->drupalRoot;
    }

    /**
     * @param string|null $drupalRoot
     *
     * @return $this
     */
    public function setDrupalRoot($drupalRoot = null)
    {
        $this->drupalRoot = $drupalRoot;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDrupalVersion()
    {
        return $this->drupalVersion;
    }

    /**
     * @param string|null $drupalVersion
     *
     * @return $this
     */
    public function setDrupalVersion($drupalVersion = null)
    {
        $this->drupalVersion = $drupalVersion;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDrupalMajorVersion()
    {
        return $this->drupalMajorVersion;
    }

    /**
     * @param int|null $drupalMajorVersion
     *
     * @return $this
     */
    public function setDrupalMajorVersion($drupalMajorVersion = null)
    {
        $this->drupalMajorVersion = $drupalMajorVersion;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdateLastCheckAt()
    {
        return $this->updateLastCheckAt;
    }

    /**
     * @param \DateTime|null $updateLastCheckAt
     *
     * @return $this
     */
    public function setUpdateLastCheckAt(\DateTime $updateLastCheckAt = null)
    {
        $this->updateLastCheckAt = $updateLastCheckAt;

        return $this;
    }

    /**
     * @return \DateTimeZone|null
     */
    public function getTimezone()
    {
        return $this->timezone === null ? null : new \DateTimeZone($this->timezone);
    }

    /**
     * @param \DateTimeZone|null $timezone
     *
     * @return $this
     */
    public function setTimezone(\DateTimeZone $timezone = null)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getExtensionsChecksum()
    {
        return $this->extensionsChecksum;
    }

    /**
     * @param string|null $extensionsChecksum
     *
     * @return $this
     */
    public function setExtensionsChecksum($extensionsChecksum = null)
    {
        $this->extensionsChecksum = $extensionsChecksum;

        return $this;
    }
}
