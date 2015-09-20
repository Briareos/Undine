<?php

namespace Undine\Drupal;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use Undine\Drupal\Exception\InstallExtensionException;
use Undine\Drupal\Exception\InvalidCredentialsException;
use Undine\Drupal\Exception\LoginFormNotFoundException;
use Undine\Drupal\Exception\ModulesFormNotFoundException;
use Undine\Drupal\Exception\OxygenPageNotFoundException;

class Client
{
    /**
     * @var GuzzleClient
     */
    private $client;

    /**
     * @param GuzzleClient $client
     */
    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param UriInterface $url
     * @param Session      $session
     *
     * @return Form
     *
     * @throws RequestException
     * @throws LoginFormNotFoundException
     */
    public function findLoginForm(UriInterface $url, Session $session)
    {
        return $this->findLoginFormAsync($url, $session)->wait();
    }

    /**
     * @param UriInterface $url
     * @param Session      $session
     *
     * @return Promise Resolves to Form.
     *
     * @throws RequestException
     * @throws LoginFormNotFoundException
     */
    public function findLoginFormAsync(UriInterface $url, Session $session)
    {
        return $this->client->getAsync($url->withQuery(\GuzzleHttp\Psr7\build_query(['q' => 'user/login'])), [
            RequestOptions::COOKIES => $session->getCookieJar(),
            RequestOptions::AUTH    => $session->getHttpCredentials(),
        ])
            ->then(function (ResponseInterface $response) use ($url) {
                $crawler = new Crawler((string)$response->getBody(), (string)$url);
                try {
                    return $crawler->filter('form#user-login')->form();
                } catch (\Exception $e) {
                    throw new LoginFormNotFoundException();
                }
            });
    }

    /**
     * @param Form    $form
     * @param string  $username
     * @param string  $password
     * @param Session $session
     *
     * @throws RequestException
     * @throws InvalidCredentialsException
     */
    public function login(Form $form, $username, $password, Session $session)
    {
        $this->loginAsync($form, $username, $password, $session)->wait();
    }

    /**
     * @param Form    $form
     * @param string  $username
     * @param string  $password
     * @param Session $session
     *
     * @return Promise Resolves to null.
     *
     * @throws RequestException
     * @throws InvalidCredentialsException
     */
    public function loginAsync(Form $form, $username, $password, Session $session)
    {
        $form->setValues([
            'name' => $username,
            'pass' => $password,
        ]);

        return $this->client->requestAsync($form->getMethod(), $form->getUri(), [
            RequestOptions::COOKIES         => $session->getCookieJar(),
            RequestOptions::AUTH            => $session->getHttpCredentials(),
            RequestOptions::FORM_PARAMS     => $form->getPhpValues(),
            RequestOptions::HEADERS         => [
                'referer' => $form->getUri(),
            ],
            RequestOptions::ALLOW_REDIRECTS => false,
        ])->then(function (ResponseInterface $response) use ($form) {
            if (substr($response->getStatusCode(), 0, 1) === '3') {
                // We got a redirect, meaning we got logged in (hopefully).
                return;
            }
            throw new InvalidCredentialsException();
        });
    }

    /**
     * @param UriInterface $url
     * @param Session      $session
     *
     * @return Form
     *
     * @throws RequestException
     * @throws ModulesFormNotFoundException
     */
    public function getModulesForm(UriInterface $url, Session $session)
    {
        return $this->getModulesFormAsync($url, $session)->wait();
    }

    /**
     * @param UriInterface $url
     * @param Session      $session
     *
     * @return Promise Resolves to Form.
     *
     * @throws RequestException
     * @throws ModulesFormNotFoundException
     */
    private function getModulesFormAsync(UriInterface $url, Session $session)
    {
        return $this->client->getAsync($url->withQuery(\GuzzleHttp\Psr7\build_query(['q' => 'admin/modules'])), [
            RequestOptions::COOKIES => $session->getCookieJar(),
            RequestOptions::AUTH    => $session->getHttpCredentials(),
        ])->then(function (ResponseInterface $response) use ($url) {
            $crawler = new Crawler((string)$response->getBody(), (string)$url);
            try {
                return $crawler->filter('form#system-modules')->form();
            } catch (\Exception $e) {
                throw new ModulesFormNotFoundException();
            }
        });
    }

    /**
     * @param Form    $form
     * @param string  $package
     * @param string  $slug
     * @param Session $session
     */
    public function enableModule(Form $form, $package, $slug, Session $session)
    {
        $this->enableModuleAsync($form, $package, $slug, $session)->wait();
    }

    /**
     * @param Form    $form
     * @param string  $package
     * @param string  $slug
     * @param Session $session
     *
     * @return Promise Resolves to null.
     */
    public function enableModuleAsync(Form $form, $package, $slug, Session $session)
    {
        $formData = $form->getPhpValues();

        if (!empty($formData['modules'][$package][$slug]['enable'])) {
            // The module is already enabled.
            return new FulfilledPromise(null);
        }

        $formData['modules'][$package][$slug]['enable'] = '1';

        return $this->client->requestAsync($form->getMethod(), $form->getUri(), [
            RequestOptions::COOKIES         => $session->getCookieJar(),
            RequestOptions::AUTH            => $session->getHttpCredentials(),
            RequestOptions::FORM_PARAMS     => $formData,
            RequestOptions::HEADERS         => [
                'referer' => $form->getUri(),
            ],
            RequestOptions::ALLOW_REDIRECTS => false,
        ])->then(function () {
            // Resolve to null.
        });
    }

    /**
     * @param Form    $form
     * @param string  $package
     * @param string  $slug
     * @param Session $session
     */
    public function disableModule(Form $form, $package, $slug, Session $session)
    {
        $this->disableModuleAsync($form, $package, $slug, $session)->wait();
    }

    /**
     * @param Form    $form
     * @param string  $package
     * @param string  $slug
     * @param Session $session
     *
     * @return Promise Resolves to null.
     */
    public function disableModuleAsync(Form $form, $package, $slug, Session $session)
    {
        $formData = $form->getPhpValues();

        if (empty($formData['modules'][$package][$slug]['enable'])) {
            // The module is already disabled.
            return new FulfilledPromise(null);
        }

        unset($formData['modules'][$package][$slug]['enable']);

        return $this->client->requestAsync($form->getMethod(), $form->getUri(), [
            RequestOptions::COOKIES         => $session->getCookieJar(),
            RequestOptions::AUTH            => $session->getHttpCredentials(),
            RequestOptions::FORM_PARAMS     => $formData,
            RequestOptions::HEADERS         => [
                'referer' => $form->getUri(),
            ],
            RequestOptions::ALLOW_REDIRECTS => false,
        ])->then(function () {
            // Resolve to null.
        });
    }

    /**
     * @param UriInterface $url
     * @param string       $extensionUrl
     * @param Session      $session
     */
    public function installExtensionFromUrl(UriInterface $url, $extensionUrl, Session $session)
    {
        $this->installExtensionFromUrlAsync($url, $extensionUrl, $session)->wait();
    }

    /**
     * @param UriInterface $url
     * @param string       $extensionUrl
     * @param Session      $session
     *
     * @return Promise Resolves to null.
     */
    public function installExtensionFromUrlAsync(UriInterface $url, $extensionUrl, Session $session)
    {
        return $this->client->getAsync($url->withQuery(\GuzzleHttp\Psr7\build_query(['q' => 'admin/modules/install'])), [
            RequestOptions::COOKIES => $session->getCookieJar(),
            RequestOptions::AUTH    => $session->getHttpCredentials(),
        ])->then(function (ResponseInterface $response) use ($url, $extensionUrl, $session) {
            $crawler = new Crawler((string)$response->getBody(), (string)$url);
            try {
                $form = $crawler->filter('form#update-manager-install-form')->form([
                    'project_url' => $extensionUrl,
                ]);
            } catch (\Exception $e) {
                throw new InstallExtensionException(InstallExtensionException::FORM_NOT_FOUND);
            }

            return $this->client->requestAsync($form->getMethod(), $form->getUri(), [
                RequestOptions::COOKIES     => $session->getCookieJar(),
                RequestOptions::AUTH        => $session->getHttpCredentials(),
                RequestOptions::HEADERS     => [
                    'referer' => $form->getUri(),
                ],
                RequestOptions::FORM_PARAMS => $form->getPhpValues(),
            ]);
        })->then(function (ResponseInterface $response) use ($session) {
            // Drupal uses batch processing when installing extensions, so follow the regular user steps.
            // Example: <meta http-equiv="Refresh" content="(\d+); URL=http://drupal-1.dev.localhost/authorize.php?batch=1&amp;id=3&amp;op=do_nojs" />
            // Send requests while we're getting HTTP-EQUIV refresh.
            $generateRequest = function (ResponseInterface $response) use ($session) {
                if (!preg_match('<meta http-equiv="Refresh" content="(\d+); URL=([^"]+)"\s*/?>', (string)$response->getBody(), $matches)) {
                    return new FulfilledPromise(null);
                }

                return $this->client->getAsync(html_entity_decode($matches[2]), [
                    RequestOptions::COOKIES => $session->getCookieJar(),
                    RequestOptions::AUTH    => $session->getHttpCredentials(),
                    RequestOptions::DELAY   => (int)$matches[1] * 1000,
                ]);
            };
            // Dynamic iterator that allows us to push requests as responses are received.
            $promises = new \ArrayIterator([$generateRequest($response)]);

            return \GuzzleHttp\Promise\each_limit_all($promises, 1, function (ResponseInterface $response = null) use ($promises, $generateRequest) {
                if ($response !== null) {
                    $promises->append($generateRequest($response));
                }
                // Resolve to null.
            });
        });
    }

    /**
     * @param UriInterface $url
     * @param Session      $session
     *
     * @return bool True if the module was just disconnected, false if it was already disconnected.
     */
    public function disconnectOxygen(UriInterface $url, Session $session)
    {
        return $this->disconnectOxygenAsync($url, $session)->wait();
    }

    /**
     * @param UriInterface $url
     * @param Session      $session
     *
     * @return Promise Resolves to boolean.
     */
    private function disconnectOxygenAsync(UriInterface $url, Session $session)
    {
        return $this->client->getAsync($url->withQuery(\GuzzleHttp\Psr7\build_query(['q' => 'admin/config/oxygen/disconnect'])), [
            RequestOptions::COOKIES => $session->getCookieJar(),
            RequestOptions::AUTH    => $session->getHttpCredentials(),
        ])->then(function (ResponseInterface $response) use ($url, $session) {
            $crawler = new Crawler((string)$response->getBody(), (string)$url);
            try {
                $form = $crawler->filter('form#oxygen-admin-disconnect')->form();
                if ($form->get('oxygen_connected')->getValue() === 'yes') {
                    return $this->client->requestAsync($form->getMethod(), $form->getUri(), [
                        RequestOptions::COOKIES     => $session->getCookieJar(),
                        RequestOptions::AUTH        => $session->getHttpCredentials(),
                        RequestOptions::HEADERS     => [
                            'referer' => $form->getUri(),
                        ],
                        RequestOptions::FORM_PARAMS => $form->getPhpValues(),
                    ]);
                }

                return false;
            } catch (\Exception $e) {
                throw new OxygenPageNotFoundException();
            }
        })->then(function ($result) {
            if (!$result instanceof ResponseInterface) {
                // Module was already disconnected.
                return false;
            }

            // Module was successfully disconnected.
            return true;
        });
    }
}
