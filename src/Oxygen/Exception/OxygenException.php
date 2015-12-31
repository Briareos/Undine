<?php

namespace Undine\Oxygen\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Undine\Oxygen\Exception\Data\ExceptionData;
use Undine\Oxygen\Exception\Data\TransferInfo;

/**
 * An exception occurred in the Oxygen module. This is a wrapper exception that contains Oxygen module's exception data.
 * Do not rely on this exception's information; instead use the self::getExceptionData() to get the underlying exception
 * info that originated on the Oxygen module.
 */
class OxygenException
{
    /**
     * @var ExceptionData
     */
    private $exceptionData;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var array
     */
    private $requestOptions;

    // @TODO continue from here: private

    /**
     * @param int               $code
     * @param ExceptionData     $exceptionData
     * @param RequestInterface  $request
     * @param array             $requestOptions
     * @param ResponseInterface $response
     * @param TransferInfo      $transferInfo
     */
    public function __construct(ExceptionData $exceptionData, RequestInterface $request, array $requestOptions, ResponseInterface $response, TransferInfo $transferInfo)
    {
        $this->exceptionData = $exceptionData;
        parent::__construct(sprintf('An error occurred in the Oxygen module: [%s] - %s'), $exceptionData->getCode());
    }

    /**
     * @return ExceptionData
     */
    public function getExceptionData()
    {
        return $this->exceptionData;
    }

    /**
     * @param array             $data
     * @param RequestInterface  $request
     * @param array             $requestOptions
     * @param ResponseInterface $response
     * @param array             $transferInfo
     *
     * @return OxygenException
     */
    public static function createFromData(array $data, RequestInterface $request, array $requestOptions, ResponseInterface $response, array $transferInfo)
    {
        $cleanData = self::getResolver()->resolve($data);
        $previous  = null;

        if ($cleanData['previous']) {
            $previous = self::getResolver()->resolve($data['previous']);
        }

        $exceptionData = new ExceptionData($data['class'], $data['message'], $data['code'], $data['type'], $data['file'], $data['line'], $data['traceString'], $data['context'], $previous);

        return new self($exceptionData, $request, $response, $transferInfo);
    }

    /**
     * @return OptionsResolver
     */
    private static function getResolver()
    {
        static $resolver;

        if ($resolver === null) {
            $resolver = (new OptionsResolver())
                ->setRequired(['class', 'message', 'context', 'file', 'line', 'code', 'type', 'traceString', 'previous'])
                ->setAllowedTypes('class', 'string')
                ->setAllowedTypes('message', 'string')
                ->setAllowedTypes('context', 'array')
                ->setAllowedTypes('file', 'string')
                ->setAllowedTypes('line', 'int')
                ->setAllowedTypes('code', 'int')
                ->setAllowedTypes('type', ['null', 'string'])
                ->setAllowedTypes('traceString', 'string')
                ->setAllowedTypes('previous', ['null', 'array']);
        }

        return $resolver;
    }
}
