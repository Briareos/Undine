<?php

namespace Undine\Guzzle\Handler;

use GuzzleHttp\Handler\CurlFactory as BaseCurlFactory;
use GuzzleHttp\Handler\EasyHandle;

class CurlFactory extends BaseCurlFactory
{
    public function release(EasyHandle $easy)
    {
        if (isset($easy->options['transfer_info']) && ($info = $easy->options['transfer_info']) && $info instanceof \ArrayObject) {
            $info->exchangeArray(curl_getinfo($easy->handle));
        }

        parent::release($easy);
    }
}
