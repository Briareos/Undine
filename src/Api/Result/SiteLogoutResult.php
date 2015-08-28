<?php

namespace Undine\Api\Result;

use Undine\Api\Serializer\Context;
use Undine\Api\Serializer\Normalizer;
use Undine\Model\Site;

class SiteLogoutResult extends AbstractResult
{
    /**
     * @var Site
     */
    private $site;

    /**
     * @var int
     */
    private $destroyedSessions;

    /**
     * @param Site $site
     * @param int  $destroyedSessions
     */
    public function __construct(Site $site, $destroyedSessions)
    {
        $this->site              = $site;
        $this->destroyedSessions = $destroyedSessions;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Normalizer $normalizer, Context $context)
    {
        return [
            'site'              => $normalizer->normalizeObject($this->site, $context),
            'destroyedSessions' => $this->destroyedSessions,
        ];
    }
}
