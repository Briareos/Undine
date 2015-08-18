<?php

namespace Undine\Api\Result;

class EmptyResult extends AbstractResult
{
    /**
     * @return array
     */
    public function getData()
    {
        return [];
    }
}
