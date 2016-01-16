<?php

namespace Undine\Api\Result;

use Undine\Api\Serializer\Context;
use Undine\Api\Serializer\Normalizer;
use Undine\Model\User;

class AuthTestResult extends AbstractResult
{
    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Normalizer $normalizer, Context $context)
    {
        return [
            'user' => $normalizer->normalizeObject($this->user, $context),
        ];
    }
}
