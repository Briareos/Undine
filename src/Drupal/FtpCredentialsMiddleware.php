<?php

namespace Undine\Drupal;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;
use Undine\Drupal\Exception\FtpCredentialsErrorException;
use Undine\Drupal\Exception\FtpCredentialsRequiredException;
use Undine\Model\Site\FtpCredentials;

class FtpCredentialsMiddleware
{
    /**
     * @var callable
     */
    private $nextHandler;

    /**
     * @param callable $nextHandler
     */
    public function __construct(callable $nextHandler)
    {
        $this->nextHandler = $nextHandler;
    }

    /**
     * @return \Closure
     */
    public static function create()
    {
        return function (callable $nextHandler) {
            return new self($nextHandler);
        };
    }

    public function __invoke(RequestInterface $request, array $options)
    {
        $fn = $this->nextHandler;

        if (empty($options['ftp_credentials'])) {
            return $fn($request, $options);
        } elseif (!$options['ftp_credentials'] instanceof FtpCredentials) {
            throw new \RuntimeException(sprintf('The "ftp_credentials" key must be an instance of "%s".', FtpCredentials::class));
        }

        $credentials = $options['ftp_credentials'];

        return $fn($request, $options)
            ->then(function (ResponseInterface $response) use ($credentials, $request, $options) {
                $crawler = new Crawler((string)$response->getBody(), (string)$request->getUri());
                try {
                    // Try to find the form.
                    $formNode = $crawler->filter('form#authorize-filetransfer-form');
                    if (!$formNode->count()) {
                        return $response;
                    }
                    // Form found - do we have the credentials?
                    if (!$credentials->present()) {
                        throw new FtpCredentialsRequiredException();
                    }
                    $ftpForm = $formNode->form();
                    if (!empty($options['__ftp_credentials_submitted'])) {
                        // The form was already submitted and we got it again - must be with an error message.
                        $error = null;
                        $errorNode = $crawler->filter('p.error');
                        if ($errorNode->count()) {
                            $error = $errorNode->text() ?: null;
                        }
                        throw new FtpCredentialsErrorException($error);
                    }
                    // connection_settings[authorize_filetransfer_default]:ftp
                    // connection_settings[ftp][username]:
                    // connection_settings[ftp][password]:
                    // connection_settings[ftp][advanced][hostname]:localhost
                    // connection_settings[ftp][advanced][port]:21
                    // connection_settings[ssh][username]:
                    // connection_settings[ssh][password]:
                    // connection_settings[ssh][advanced][hostname]:localhost
                    // connection_settings[ssh][advanced][port]:22
                    $submitValues = $ftpForm->getValues();
                    // This is not handled by the form component, but Drupal requires this button to be pressed.
                    // The "Continue" string does not have to be translated upon continuing, it's just here for
                    // the convenience.
                    $submitValues['process_updates'] = 'Continue';

                    $submitValues['connection_settings']['authorize_filetransfer_default'] = $credentials->getMethod();
                    $submitValues['connection_settings'][$credentials->getMethod()] = [
                        'username' => $credentials->getUsername(),
                        'password' => $credentials->getPassword(),
                        'advanced' => [
                            'hostname' => $credentials->getHost(),
                            'port' => $credentials->getPort(),
                        ],
                    ];
                } catch (FtpCredentialsRequiredException $e) {
                    throw $e;
                } catch (FtpCredentialsErrorException $e) {
                    throw $e;
                } catch (\Exception $e) {
                    // The form was not found - continue.
                    return $response;
                }

                $newRequest = $request->withMethod($ftpForm->getMethod())
                    ->withUri(new Uri($ftpForm->getUri()))
                    ->withBody(\GuzzleHttp\Psr7\stream_for(http_build_query($submitValues, null, '&')))
                    ->withHeader('content-type', 'application/x-www-form-urlencoded');

                $options['__ftp_credentials_submitted'] = true;

                return $this($newRequest, $options);
            });
    }
}
