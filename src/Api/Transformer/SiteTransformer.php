<?php

namespace Undine\Api\Transformer;

use Undine\Model\Site;
use Undine\Model\SiteState;
use Undine\Model\User;

class SiteTransformer extends AbstractTransformer
{
    protected $availableIncludes = [
        'user',
        'state',
    ];

    public function transform(Site $site)
    {
        return [
            'id'  => $site->getId(),
            'url' => (string)$site->getUrl(),
        ];
    }

    public function includeUser(Site $site)
    {
        return $this->item($site->getUser(), $this->transformers->get(User::class));
    }

    public function includeState(Site $site)
    {
        return $this->item($site->getSiteState(), $this->transformers->get(SiteState::class));
    }
}
