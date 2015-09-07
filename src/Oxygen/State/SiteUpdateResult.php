<?php

namespace Undine\Oxygen\State;

use Undine\Model\Site;

/**
 * @property Site        $site
 * @property string      $slug
 * @property string      $type
 * @property string      $name
 * @property string|null $project
 * @property string|null $package
 * @property string      $existingVersion
 * @property string      $recommendedVersion
 * @property string      $recommendedDownloadLink
 * @property string      $status
 * @property string[]    $includes
 * @property bool        $enabled
 * @property string[]    $baseThemes
 * @property string[]    $subThemes
 */
class SiteUpdateResult
{
    use StateResultTrait;
}
