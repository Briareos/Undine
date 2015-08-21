<?php

namespace Undine\Api\Transformer;

use League\Fractal\ParamBag;
use Undine\Model\Site;
use Undine\Model\User;

class SiteTransformer extends AbstractTransformer
{
    protected $availableIncludes = [
        'user',
    ];

    public function transform(Site $site)
    {
        return [
            'uid' => $site->getUid(),
            'url' => (string)$site->getUrl(),
        ];
    }

    public function includeUser(
        Site $site,
        /** @noinspection PhpUnusedParameterInspection */
        ParamBag $paramBag = null
    ) {
        return $this->item($site->getUser(), $this->transformers->get(User::class));
    }
}
