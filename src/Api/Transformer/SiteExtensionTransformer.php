<?php

namespace Undine\Api\Transformer;

use Undine\Model\SiteExtension;

class SiteExtensionTransformer extends AbstractTransformer
{
    public function transform(SiteExtension $siteExtension)
    {
        return [
            'type'        => $siteExtension->getType(),
            'name'        => $siteExtension->getName(),
            'description' => $siteExtension->getDescription(),
            'version'     => $siteExtension->getVersion(),
            'required'    => $siteExtension->isRequired(),
            'enabled'     => $siteExtension->isEnabled(),
            'package'     => $siteExtension->getPackage(),
            'project'     => $siteExtension->getPackage(),
        ];
    }
}
