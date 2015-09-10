<?php

namespace Undine\Oxygen\State;

/**
 * @property string      $filename
 * @property string      $type
 * @property string      $slug
 * @property string|null $parent
 * @property bool        $enabled
 * @property string      $name
 * @property string      $description
 * @property string|null $package
 * @property string|null $version
 * @property bool        $required
 * @property string[]    $dependencies
 * @property string      $project
 */
class SiteExtensionResult
{
    use StateResultTrait;
}
