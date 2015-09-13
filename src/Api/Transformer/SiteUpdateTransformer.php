<?php

namespace Undine\Api\Transformer;

use Undine\Model\SiteUpdate;

class SiteUpdateTransformer extends AbstractTransformer
{
    public function transform(SiteUpdate $siteUpdate)
    {
        return [
            'name'               => $siteUpdate->getName(),
            'type'               => $siteUpdate->getType(),
            'slug'               => $siteUpdate->getSlug(),
            'existingVersion'    => $siteUpdate->getExistingVersion(),
            'recommendedVersion' => $siteUpdate->getRecommendedVersion(),
            'status'             => $siteUpdate->getStatus(),
            'enabled'            => $siteUpdate->isEnabled(),
            'package'            => $siteUpdate->getPackage(),
            'project'            => $siteUpdate->getProject(),
            'includes'           => $siteUpdate->getIncludes(),
            'baseThemes'         => $siteUpdate->getBaseThemes(),
            'subThemes'          => $siteUpdate->getSubThemes(),
        ];
    }
}
