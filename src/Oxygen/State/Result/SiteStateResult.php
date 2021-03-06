<?php

namespace Undine\Oxygen\State\Result;

/**
 * @property string                $siteKey
 * @property string                $cronKey
 * @property \DateTime             $cronLastRunAt
 * @property string                $siteMail
 * @property string                $siteName
 * @property string                $siteRoot
 * @property string                $drupalRoot
 * @property string                $drupalVersion
 * @property int                   $drupalMajorVersion
 * @property \DateTimezone|null    $timezone
 * @property string                $phpVersion
 * @property int                   $phpVersionId
 * @property string                $databaseDriver
 * @property string                $databaseDriverVersion
 * @property string                $databaseTablePrefix
 * @property int                   $memoryLimit
 * @property int                   $processArchitecture
 * @property string                $internalIp
 * @property string                $uname
 * @property string                $hostname
 * @property string                $os
 * @property bool                  $windows
 * @property string                $extensionsChecksum
 * @property bool                  $extensionsCacheHit
 * @property SiteExtensionResult[] $extensions
 * @property \DateTime             $updatesLastCheckAt
 * @property SiteUpdateResult[]    $updates
 */
class SiteStateResult
{
    use StateResultTrait;
}
