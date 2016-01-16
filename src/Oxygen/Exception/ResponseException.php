<?php

namespace Undine\Oxygen\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Undine\Oxygen\Exception\Data\TransferInfo;

class ResponseException extends ProtocolException
{
    const BODY_TOO_LARGE = 20001;
    const RESPONSE_NOT_FOUND = 20002;
    const RESPONSE_INVALID_JSON = 20003;
    const RESPONSE_MALFORMED = 20004;
    const RESPONSE_NOT_AN_ARRAY = 20005;
    const ACTION_RESULT_NOT_ARRAY = 20006;
    const STATE_NOT_ARRAY = 20007;
    const EXCEPTION_NOT_ARRAY = 20008;
    const RESULT_NOT_FOUND = 20009;
    const MALFORMED_EXCEPTION = 20010;
    const STATE_EMPTY = 20011;
    const STATE_MALFORMED = 20012;
    const ACTION_RESULT_MALFORMED = 20013;

    /**
     * @var string
     */
    private $type;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var array
     */
    private $requestOptions;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var TransferInfo
     */
    private $transferInfo;

    private static $codes;

    /**
     * @param int               $code
     * @param RequestInterface  $request
     * @param array             $requestOptions
     * @param ResponseInterface $response
     * @param TransferInfo      $transferInfo
     * @param \Exception|null   $previous
     *
     * @throws \OutOfRangeException If the code is not recognized.
     */
    public function __construct($code, RequestInterface $request, array $requestOptions, ResponseInterface $response, TransferInfo $transferInfo, \Exception $previous = null)
    {
        $this->type = self::getTypeForCode($code);
        $this->request = $request;
        $this->requestOptions = $requestOptions;
        $this->response = $response;
        $this->transferInfo = $transferInfo;
        parent::__construct(sprintf('An error occurred while parsing the response: %s', $this->type), $code, $previous);
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return self::LEVEL_RESPONSE;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function getRequestOptions()
    {
        return $this->requestOptions;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return TransferInfo
     */
    public function getTransferInfo()
    {
        return $this->transferInfo;
    }

    /**
     * @param int $code
     *
     * @return string
     *
     * @throws \OutOfRangeException If the code is not recognized.
     */
    private static function getTypeForCode($code)
    {
        if (!isset(self::$codes)) {
            $reflectionClass = new \ReflectionClass(__CLASS__);
            self::$codes = array_flip($reflectionClass->getConstants());
        }

        if (array_key_exists($code, self::$codes)) {
            return self::$codes[$code];
        }

        throw new \OutOfRangeException(sprintf('The error code %d is not registered.', $code));
    }
}
