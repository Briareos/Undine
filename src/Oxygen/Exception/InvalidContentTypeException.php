<?php

namespace Undine\Oxygen\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class InvalidContentTypeException extends InvalidResponseException
{
    /**
     * @var string
     */
    private $expectedContentType;
    /**
     * @var string
     */
    private $providedContentType;

    /**
     * @param string            $expectedContentType
     * @param string            $providedContentType
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $options
     */
    public function __construct($expectedContentType, $providedContentType, RequestInterface $request, ResponseInterface $response, array $options)
    {
        $this->providedContentType = $providedContentType;
        $this->expectedContentType = $expectedContentType;

        parent::__construct(sprintf('Expected content type of "%s", got "%s".', $expectedContentType, $providedContentType), $request, $response, null, $options);
    }

    /**
     * @return string
     */
    public function getExpectedContentType()
    {
        return $this->expectedContentType;
    }

    /**
     * @return string
     */
    public function getProvidedContentType()
    {
        return $this->providedContentType;
    }
}
