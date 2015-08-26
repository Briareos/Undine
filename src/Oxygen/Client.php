<?php

namespace Undine\Oxygen;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;
use Undine\Model\Site;
use Undine\Oxygen\Action\ActionInterface;
use Undine\Oxygen\Middleware\OxygenProtocolMiddleware;
use Undine\Oxygen\Reaction\ReactionInterface;

class Client
{
    /**
     * @var callable
     */
    private $handler;

    /**
     * @var callable
     */
    private $protocolMiddleware;

    /**
     * @var HttpClient|null
     */
    private $cachedClient;

    /**
     * @param callable $handler
     * @param callable $protocolMiddleware
     */
    public function __construct(callable $handler, callable $protocolMiddleware)
    {
        $this->handler            = $handler;
        $this->protocolMiddleware = $protocolMiddleware;
    }

    /**
     * @return HttpClient
     */
    private function getHttpClient()
    {
        if ($this->cachedClient === null) {
            $stack = new HandlerStack($this->handler);
            $stack->push($this->protocolMiddleware, 'oxygen_protocol');
            $stack->push(Middleware::redirect(), 'allow_redirects');
            $stack->push(Middleware::cookies(), 'cookies');
            $stack->push(Middleware::prepareBody(), 'prepare_body');

            $this->cachedClient = new HttpClient(['handler' => $stack->resolve()]);
        }

        return $this->cachedClient;
    }

    /**
     * @param Site            $site
     * @param ActionInterface $action
     * @param array           $options
     *
     * @return ReactionInterface
     * @throws
     */
    public function send(Site $site, ActionInterface $action, array $options = [])
    {
        return $this->sendAsync($site, $action, $options)->wait();
    }

    /**
     * @param Site            $site
     * @param ActionInterface $action
     * @param array           $options
     *
     * @return Promise
     */
    public function sendAsync(Site $site, ActionInterface $action, array $options = [])
    {
        $options['oxygen_site']   = $site;
        $options['oxygen_action'] = $action;

        $request = new Request('POST', $site->getUrl(), ['cookie' => 'XDEBUG_SESSION=PHPSTORM']);

        return $this->getHttpClient()->sendAsync($request, $options);
    }
}
