<?php

namespace Undine\Api\Result;

use Undine\Api\Serializer\Context;
use Undine\Api\Serializer\Normalizer;
use Undine\Model\Site;

class SiteConnectResult extends AbstractResult
{
    /**
     * @var Site
     */
    private $site;

    /**
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Normalizer $normalizer, Context $context)
    {
        return [
            'site' => $normalizer->normalizeObject($this->site, $context),
        ];
    }
}
