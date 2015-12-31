<?php

namespace Undine\Api\Result;

use Psr\Http\Message\UriInterface;
use Undine\Api\Serializer\Context;
use Undine\Api\Serializer\Normalizer;
use Undine\Model\Site;

class SiteLoginResult extends AbstractResult
{
    /**
     * @var Site
     */
    private $site;

    /**
     * @var UriInterface
     */
    private $loginUrl;

    /**
     * @param Site         $site
     * @param UriInterface $loginUrl
     */
    public function __construct(Site $site, UriInterface $loginUrl)
    {
        $this->site     = $site;
        $this->loginUrl = $loginUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Normalizer $normalizer, Context $context)
    {
        return [
            'site'     => $normalizer->normalizeObject($this->site, $context),
            'loginUrl' => (string)$this->loginUrl,
        ];
    }
}
