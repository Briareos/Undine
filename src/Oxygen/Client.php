<?php

namespace Undine\Oxygen;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
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
     * @param callable $handler
     */
    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param Site            $site
     * @param ActionInterface $action
     * @param array           $options
     *
     * @return ReactionInterface
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
        $options += [
            'oxygen_site'=>$site,
            'oxygen_action'=>$action,
            RequestOptions::ALLOW_REDIRECTS => [
                'max'      => 1,
                // Strict redirect following means that you follow up with a GET request; don't do that.
                'strict'   => true,
                'referrer' => true,
            ],
            RequestOptions::VERIFY=>false,
        ];

        $request = new Request('POST', $site->getUrl(), ['cookie' => 'XDEBUG_SESSION=PHPSTORM']);

        $fn = $this->handler;

        return $fn($request, $options);
    }
}
