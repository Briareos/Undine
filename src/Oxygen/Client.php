<?php

namespace Undine\Oxygen;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\Promise;
use Undine\Oxygen\Action\ActionInterface;
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
        $stack = new HandlerStack($handler);
        $stack->push(Middleware::redirect(), 'allow_redirects');
        $stack->push(Middleware::cookies(), 'cookies');
        $stack->push(Middleware::prepareBody(), 'prepare_body');
        $http = new HttpClient();


        $this->httpClient = $httpClient;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return ActionInterface
     */
    public function createAction($name, array $arguments = [])
    {

    }

    /**
     * @param ActionInterface $action
     * @param array           $options
     *
     * @return ReactionInterface
     */
    public function send(ActionInterface $action, array $options = [])
    {
        return $this->sendAsync($action, $options)->wait();
    }

    /**
     * @param ActionInterface $action
     * @param array           $options
     *
     * @return Promise
     */
    public function sendAsync(ActionInterface $action, array $options = [])
    {
        
    }
}
