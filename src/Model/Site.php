<?php

namespace Undine\Model;

use Psr\Http\Message\UriInterface;
use Undine\Uid\UidInterface;
use Undine\Uid\UidTrait;

class Site implements UidInterface
{
    use UidTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var UriInterface
     */
    private $url;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var string
     */
    private $publicKey;

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
     * Name of the database.
     *
     * @var string|null
     */
    private $databaseSchema;

    /**
     * One of 'mysql', 'pdosql', 'sqlite'.
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
     * @var string|null
     */
    private $drupalRoot;

    /**
     * @var string|null
     */
    private $drupalVersion;

    /**
     * @var \DateTime|null
     */
    private $updateLastCheckAt;

    /**
     * @var string|null
     */
    private $systemChecksum;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     */
    private $deletedAt;

    /**
     * @param UriInterface $url
     * @param User         $user
     * @param string       $privateKey
     * @param string       $publicKey
     */
    public function __construct(UriInterface $url, User $user, $privateKey, $publicKey)
    {
        $this->url        = $url;
        $this->user       = $user;
        $this->privateKey = $privateKey;
        $this->publicKey  = $publicKey;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return UriInterface
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param UriInterface $url
     *
     * @return $this
     */
    public function setUrl(UriInterface $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

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
    public function getDatabaseSchema()
    {
        return $this->databaseSchema;
    }

    /**
     * @param string|null $databaseSchema
     *
     * @return $this
     */
    public function setDatabaseSchema($databaseSchema = null)
    {
        $this->databaseSchema = $databaseSchema;

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
     * @return string|null
     */
    public function getSystemChecksum()
    {
        return $this->systemChecksum;
    }

    /**
     * @param string|null $systemChecksum
     *
     * @return $this
     */
    public function setSystemChecksum($systemChecksum = null)
    {
        $this->systemChecksum = $systemChecksum;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
}
