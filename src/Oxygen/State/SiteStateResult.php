<?php

namespace Undine\Oxygen\State;

/**
 * @property string        $siteKey
 * @property string        $cronKey
 * @property \DateTime     $cronLastRunAt
 * @property string        $siteMail
 * @property string        $siteName
 * @property string        $siteRoot
 * @property string        $drupalRoot
 * @property string        $drupalVersion
 * @property \DateTime     $updateLastCheckAt
 * @property \DateTimezone $timezone
 * @property string        $phpVersion
 * @property int           $phpVersionId
 * @property string        $databaseDriver
 * @property string        $databaseDriverVersion
 * @property string        $databaseTablePrefix
 * @property int           $memoryLimit
 * @property int           $processArchitecture
 * @property string        $internalIp
 * @property string        $uname
 * @property string        $hostname
 * @property string        $os
 * @property bool          $windows
 * @property string        $systemChecksum
 * @property bool          $systemCacheHit
 * @property array         $systemData
 */
class SiteStateResult
{
    /**
     * @var array
     */
    private $data;
    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    /**
     * {@inheritdoc}
     */
    function __get($name)
    {
        if (!array_key_exists($name, $this->data)) {
            throw new \OutOfBoundsException(sprintf('Property "%s" could not be found.'));
        }
        return $this->data[$name];
    }
    /**
     * {@inheritdoc}
     */
    function __isset($name)
    {
        return array_key_exists($this->data, $name);
    }
}
