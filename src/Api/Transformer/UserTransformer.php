<?php

namespace Undine\Api\Transformer;

use League\Fractal\ParamBag;
use Undine\Model\Site;
use Undine\Model\User;

class UserTransformer extends AbstractTransformer
{
    protected $availableIncludes = [
        'sites',
    ];

    public function transform(User $user)
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'emailMd5' => md5($user->getEmail()),
        ];
    }

    public function includeSites(
        User $user,
        /* @noinspection PhpUnusedParameterInspection */
        ParamBag $paramBag = null
    ) {
        return $this->collection($user->getSites(), $this->transformers->get(Site::class));
    }
}
