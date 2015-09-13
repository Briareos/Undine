<?php

namespace Undine\Api\Transformer;

use Undine\Model\Site\SiteState;

class SiteStateTransformer extends AbstractTransformer
{
    public function transform(SiteState $siteState)
    {
        return [
            'drupalVersion' => $siteState->getDrupalVersion(),
        ];
    }
}
