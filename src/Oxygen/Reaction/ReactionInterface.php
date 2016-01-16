<?php

namespace Undine\Oxygen\Reaction;

use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;

interface ReactionInterface
{
    /**
     * @param array $data The data, as returned by the action.
     *
     * @throws ExceptionInterface If the data format is invalid.
     *                            This should never be encountered no non-hacked Oxygen modules.
     */
    public function setData(array $data);
}
