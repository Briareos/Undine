<?php

namespace Undine\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher;

/**
 * Completely the same as RequestMatcher, but supports checking for present headers.
 */
class HeaderAwareRequestMatcher extends RequestMatcher
{
    /**
     * @var string[]
     */
    private $headers = [];

    /**
     * @param string|null          $path
     * @param string|null          $host
     * @param string|string[]|null $methods
     * @param string|string[]|null $ips
     * @param array                $attributes
     * @param string|string[]|null $schemes
     * @param string|string[]|null $headers Header names to check for presence in the request.
     */
    public function __construct($path = null, $host = null, $methods = null, $ips = null, array $attributes = [], $schemes = null, $headers = null)
    {
        parent::__construct($path, $host, $methods, $ips, $attributes, $schemes);
        $this->matchHeaders($headers);
    }

    /**
     * Header names to check for presence in the request.
     *
     * @param string|string[]|null $headers
     */
    public function matchHeaders($headers)
    {
        $this->headers = (array)$headers;
    }

    /**
     * {@inheritdoc}
     */
    public function matches(Request $request)
    {
        foreach ($this->headers as $header) {
            if (!$request->headers->has($header)) {
                return false;
            }
        }

        return parent::matches($request);
    }
}
