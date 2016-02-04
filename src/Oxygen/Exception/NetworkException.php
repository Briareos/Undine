<?php

namespace Undine\Oxygen\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Undine\Oxygen\Exception\Data\TransferInfo;

/**
 * The HTTP communication with the server did not successfully complete. The response may be present, but it's unreliable.
 */
class NetworkException extends ProtocolException
{
    /**
     * Add this value to any error code to be consistent with other error codes.
     */
    const SHIFT_ERROR_CODE = 10000;

    /**
     * Generated using the following snippet:.
     *
     *     array_filter(get_defined_constants(true)['curl'], function ($key) {
     *         return strncmp($key, 'CURLE_', 6) === 0;
     *     }, ARRAY_FILTER_USE_KEY);
     *
     * with the following errors being removed (left side is deprecated/removed):
     * - CURLE_OPERATION_TIMEOUTED => CURLE_OPERATION_TIMEDOUT
     * - CURLE_HTTP_NOT_FOUND => CURLE_HTTP_RETURNED_ERROR
     * - All the CURLE_FTP_* constants removed
     * - CURLE_OK removed
     */
    private static $errors = [
        42 => 'CURLE_ABORTED_BY_CALLBACK',
        44 => 'CURLE_BAD_CALLING_ORDER',
        61 => 'CURLE_BAD_CONTENT_ENCODING',
        36 => 'CURLE_BAD_DOWNLOAD_RESUME',
        43 => 'CURLE_BAD_FUNCTION_ARGUMENT',
        46 => 'CURLE_BAD_PASSWORD_ENTERED',
        7 => 'CURLE_COULDNT_CONNECT',
        6 => 'CURLE_COULDNT_RESOLVE_HOST',
        5 => 'CURLE_COULDNT_RESOLVE_PROXY',
        2 => 'CURLE_FAILED_INIT',
        37 => 'CURLE_FILE_COULDNT_READ_FILE',
        41 => 'CURLE_FUNCTION_NOT_FOUND',
        52 => 'CURLE_GOT_NOTHING',
        45 => 'CURLE_HTTP_PORT_FAILED',
        34 => 'CURLE_HTTP_POST_ERROR',
        33 => 'CURLE_HTTP_RANGE_ERROR',
        22 => 'CURLE_HTTP_RETURNED_ERROR',
        38 => 'CURLE_LDAP_CANNOT_BIND',
        39 => 'CURLE_LDAP_SEARCH_FAILED',
        40 => 'CURLE_LIBRARY_NOT_FOUND',
        24 => 'CURLE_MALFORMAT_USER',
        50 => 'CURLE_OBSOLETE',
        28 => 'CURLE_OPERATION_TIMEDOUT',
        27 => 'CURLE_OUT_OF_MEMORY',
        18 => 'CURLE_PARTIAL_FILE',
        26 => 'CURLE_READ_ERROR',
        56 => 'CURLE_RECV_ERROR',
        55 => 'CURLE_SEND_ERROR',
        57 => 'CURLE_SHARE_IN_USE',
        60 => 'CURLE_SSL_CACERT',
        58 => 'CURLE_SSL_CERTPROBLEM',
        59 => 'CURLE_SSL_CIPHER',
        35 => 'CURLE_SSL_CONNECT_ERROR',
        53 => 'CURLE_SSL_ENGINE_NOTFOUND',
        54 => 'CURLE_SSL_ENGINE_SETFAILED',
        51 => 'CURLE_SSL_PEER_CERTIFICATE',
        49 => 'CURLE_TELNET_OPTION_SYNTAX',
        47 => 'CURLE_TOO_MANY_REDIRECTS',
        48 => 'CURLE_UNKNOWN_TELNET_OPTION',
        1 => 'CURLE_UNSUPPORTED_PROTOCOL',
        3 => 'CURLE_URL_MALFORMAT',
        4 => 'CURLE_URL_MALFORMAT_USER',
        23 => 'CURLE_WRITE_ERROR',
        63 => 'CURLE_FILESIZE_EXCEEDED',
        62 => 'CURLE_LDAP_INVALID_URL',
        64 => 'CURLE_FTP_SSL_FAILED',
        79 => 'CURLE_SSH',
    ];

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var array
     */
    private $requestOptions;

    /**
     * @var ResponseInterface|null
     */
    private $response;

    /**
     * @var TransferInfo
     */
    private $transferInfo;

    /**
     * @param int                    $code
     * @param RequestInterface       $request
     * @param array                  $requestOptions
     * @param ResponseInterface|null $response
     * @param TransferInfo           $transferInfo
     *
     * @throws \InvalidArgumentException If the error code is invalid.
     */
    public function __construct($code, RequestInterface $request, array $requestOptions, ResponseInterface $response = null, TransferInfo $transferInfo)
    {
        if (!isset(self::$errors[$code])) {
            throw new \InvalidArgumentException(sprintf('Error code %d is not recognized.', $code));
        }

        $this->request = $request;
        $this->requestOptions = $requestOptions;
        $this->response = $response;
        $this->transferInfo = $transferInfo;
        parent::__construct(sprintf('An error occurred in the network layer: %s', self::$errors[$code]), $code + self::SHIFT_ERROR_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return self::LEVEL_NETWORK;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::$errors[$this->code - self::SHIFT_ERROR_CODE];
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
     * @return ResponseInterface|null
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
     * {@inheritdoc}
     */
    public function is($code)
    {
        // Since we shifted the error code, reverse that here.
        return parent::is($code - self::SHIFT_ERROR_CODE);
    }

    /**
     * @return int
     */
    public function getOriginalCode()
    {
        return $this->code - self::SHIFT_ERROR_CODE;
    }
}
