<?php

namespace Undine\Api\Result;

use Undine\Api\Serializer\Context;
use Undine\Api\Serializer\Normalizer;

class SiteDisconnectResult extends AbstractResult
{
    /**
     * @var bool
     */
    private $oxygenDeactivated;

    public function __construct($oxygenDeactivated)
    {
        $this->oxygenDeactivated = $oxygenDeactivated;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Normalizer $normalizer, Context $context)
    {
        return [
            'oxygenDeactivated' => $this->oxygenDeactivated,
        ];
    }
}
