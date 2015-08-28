<?php

namespace Undine\Oxygen\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * An exception that originated on the Oxygen module.
 */
class OxygenException extends ProtocolException
{
    const GENERAL_ERROR = 10000;

    const FATAL_ERROR = 10046;

    const RSA_KEY_OPENSSL_VERIFY_ERROR = 10001;

    const RSA_KEY_PARSING_FAILED = 10002;
    const RSA_KEY_MISSING_ASN1_SEQUENCE = 10003;
    const RSA_KEY_MISSING_ASN1_OBJECT = 10004;
    const RSA_KEY_MISSING_ASN1_BITSTRING = 10005;
    const RSA_KEY_MISSING_ASN1_INTEGER = 10006;
    const RSA_KEY_INVALID_LENGTH = 10007;
    const RSA_KEY_UNSUPPORTED_ENCRYPTION = 10008;
    const RSA_KEY_SIGNATURE_REPRESENTATIVE_OUT_OF_RANGE = 10009;
    const RSA_KEY_SIGNATURE_INVALID = 10010;
    const RSA_KEY_INVALID_FORMAT = 10011;
    const RSA_KEY_SIGNATURE_SIZE_INVALID = 10012;
    const RSA_KEY_MODULUS_SIZE_INVALID = 10013;
    const RSA_KEY_ENCODED_SIZE_INVALID = 10014;

    const ACTION_NOT_FOUND = 10015;
    const ACTION_ARGUMENT_EMPTY = 10024;

    const NONCE_EXPIRED = 10017;
    const NONCE_ALREADY_USED = 10018;

    const HANDSHAKE_VERIFY_TEST_FAILED = 10022;
    const HANDSHAKE_VERIFY_FAILED = 10023;
    const HANDSHAKE_LOCAL_KEY_NOT_FOUND = 10042;
    const HANDSHAKE_LOCAL_VERIFY_FAILED = 10043;

    const PROTOCOL_PUBLIC_KEY_NOT_PROVIDED = 10019;
    const PROTOCOL_PUBLIC_KEY_NOT_VALID = 10025;
    const PROTOCOL_SIGNATURE_NOT_VALID = 10026;
    const PROTOCOL_SIGNATURE_NOT_PROVIDED = 10020;
    const PROTOCOL_EXPIRATION_NOT_PROVIDED = 10021;
    const PROTOCOL_EXPIRATION_NOT_VALID = 10027;
    const PROTOCOL_ACTION_NAME_NOT_PROVIDED = 10030;
    const PROTOCOL_ACTION_NAME_NOT_VALID = 10031;
    const PROTOCOL_ACTION_PARAMETERS_NOT_PROVIDED = 10032;
    const PROTOCOL_ACTION_PARAMETERS_NOT_VALID = 10033;
    const PROTOCOL_REQUIRED_VERSION_NOT_PROVIDED = 10028;
    const PROTOCOL_REQUIRED_VERSION_NOT_VALID = 10029;
    const PROTOCOL_VERSION_TOO_LOW = 10034;
    const PROTOCOL_HANDSHAKE_KEY_NOT_PROVIDED = 10035;
    const PROTOCOL_HANDSHAKE_KEY_NOT_VALID = 10036;
    const PROTOCOL_HANDSHAKE_SIGNATURE_NOT_PROVIDED = 10037;
    const PROTOCOL_HANDSHAKE_SIGNATURE_NOT_VALID = 10038;
    const PROTOCOL_BASE_URL_NOT_PROVIDED = 10039;
    const PROTOCOL_BASE_URL_NOT_VALID = 10040;
    const PROTOCOL_BASE_URL_SLUG_MISMATCHES = 10041;
    const PROTOCOL_REQUEST_ID_NOT_PROVIDED = 10044;
    const PROTOCOL_REQUEST_ID_NOT_VALID = 10045;
    const PROTOCOL_USERNAME_NOT_PROVIDED = 10049;
    const PROTOCOL_USERNAME_NOT_VALID = 10048;
    const PROTOCOL_USER_UID_NOT_PROVIDED = 10050;
    const PROTOCOL_USER_UID_NOT_VALID = 10051;

    const PUBLIC_KEY_MISSING = 10046;

    const AUTO_LOGIN_CAN_NOT_FIND_USER = 10047;

    /**
     * @var string|null
     */
    private $class;

    /**
     * @var string|null
     */
    private $type;

    /**
     * @var array|null
     */
    private $context;

    /**
     * @var string|null
     */
    private $traceString;

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $handlerContext
     * @param string            $class
     * @param string            $message
     * @param int               $code
     * @param string|null       $type
     * @param array|null        $context
     * @param string|null       $file
     * @param int|null          $line
     * @param string|null       $traceString
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, array $handlerContext, $class, $message, $code, $type = null, array $context = null, $file = null, $line = null, $traceString = null)
    {
        parent::__construct($message, $request, $response, null, $handlerContext);

        $this->class       = $class;
        $this->code        = $code;
        $this->file        = $file;
        $this->line        = $line;
        $this->type        = $type;
        $this->context     = $context;
        $this->traceString = $traceString;
    }

    public function getExceptionClass()
    {
        return $this->class;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getExceptionContext()
    {
        return $this->context;
    }

    public function getExceptionTraceAsString()
    {
        return $this->traceString;
    }

    /**
     * @param string            $path
     * @param array             $responseData
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $requestOptions
     *
     * @return OxygenException
     */
    public static function createFromResponseData($path, array $responseData, RequestInterface $request, ResponseInterface $response, array $requestOptions)
    {
        list($class, $message, $code, $type, $context, $file, $line, $traceString) = self::extractResponseData($path, $responseData, $request, $response, $requestOptions);

        return new self($request, $response, $requestOptions, $class, $message, $code, $type, $context, $file, $line, $traceString);
    }

    /**
     * Mostly redundant checks, but for all we know this is user-provided input, so we must check every little thing.
     *
     * @param string            $path
     * @param array             $responseData
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $requestOptions
     *
     * @return array
     */
    private static function extractResponseData($path, array $responseData, RequestInterface $request, ResponseInterface $response, array $requestOptions)
    {
        $responseData += [
            'class'       => null,
            'message'     => null,
            'code'        => null,
            'type'        => null,
            'context'     => null,
            'file'        => null,
            'line'        => null,
            'traceString' => null,
        ];

        if (!isset($responseData['class']) || !is_string($responseData['class'])) {
            throw new InvalidBodyException(sprintf('%s.class should be a string, got %s.', $path, gettype($responseData['class'])), $request, $response, null, $requestOptions);
        }

        if (!isset($responseData['message']) || !is_string($responseData['message'])) {
            throw new InvalidBodyException(sprintf('%s.message should be a string, got %s.', $path, gettype($responseData['message'])), $request, $response, null, $requestOptions);
        }

        if (!isset($responseData['code']) || !is_int($responseData['code'])) {
            throw new InvalidBodyException(sprintf('%s.code should be an integer, got %s.', $path, gettype($responseData['code'])), $request, $response, null, $requestOptions);
        }

        if (isset($responseData['traceString']) && !is_string($responseData['traceString'])) {
            throw new InvalidBodyException(sprintf('%s.code should be a string, got %s.', $path, gettype($responseData['traceString'])), $request, $response, null, $requestOptions);
        }

        if (isset($responseData['type']) && !is_string($responseData['type'])) {
            throw new InvalidBodyException(sprintf('%s.type should be a string, got %s.', $path, gettype($responseData['type'])), $request, $response, null, $requestOptions);
        }

        if (isset($responseData['type'])) {
            $constant = 'self::'.$responseData['type'];
            if (!defined($constant)) {
                throw new InvalidBodyException(sprintf('%s.type map to a valid constant, constant [%s] could not be found.', $path, $responseData['type']), $request, $response, null, $requestOptions);
            }
            $code = constant($constant);

            if ($code !== $responseData['code']) {
                throw new InvalidBodyException(sprintf('%s.type code should map to error code %s, got %s.', $path, $code, $responseData['code']), $request, $response, null, $requestOptions);
            }

            if (isset($responseData['context']) && !is_array($responseData['context'])) {
                throw new InvalidBodyException(sprintf('%s.context should be an array, got %s.', $path, gettype($responseData['context'])), $request, $response, null, $requestOptions);
            }
        }

        return [
            $responseData['class'],
            $responseData['message'],
            $responseData['code'],
            $responseData['type'],
            $responseData['context'],
            $responseData['file'],
            $responseData['line'],
            $responseData['traceString'],
        ];
    }
}
