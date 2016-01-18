<?php

namespace Undine\Api\Result;

use Undine\Api\Serializer\Context;
use Undine\Api\Serializer\Normalizer;
use Undine\Model\SiteState;

class SitePingResult extends AbstractResult
{
    /**
     * @var SiteState
     */
    private $siteState;

    public function __construct(SiteState $siteState)
    {
        $this->siteState = $siteState;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Normalizer $normalizer, Context $context)
    {
        return [
            'siteState' => $normalizer->normalizeObject($this->siteState, $context),
        ];
    }
}
