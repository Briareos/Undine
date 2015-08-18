<?php

namespace Undine\Configuration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class ApiResult extends ConfigurationAnnotation
{
    public function getAliasName()
    {
        return 'api_result';
    }

    public function allowArray()
    {
        return false;
    }
}
