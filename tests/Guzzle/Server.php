<?php

namespace Undine\Tests\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use Psr\Http\Message\ResponseInterface;

class Server
{
    const REQUEST_DELIMITER = "\n----[request]\n";

    /** @var Client */
    private static $client;

    public static $started;

    public static $url = 'http://127.0.0.1:8125/';

    public static $port = 8125;

    /**
     * Flush the received requests from the server
     *
     * @throws \RuntimeException
     */
    public static function flush()
    {
        self::start();

        return self::$client->delete('guzzle-server/requests');
    }

    /**
     * Queue an array of responses or a single response on the server.
     *
     * Any currently queued responses will be overwritten.  Subsequent requests
     * on the server will return queued responses in FIFO order.
     *
     * @param array|ResponseInterface $responses A single or array of Responses
     *                                           to queue.
     *
     * @throws \Exception
     */
    public static function enqueue($responses)
    {
        self::start();

        $data = [];
        foreach ((array) $responses as $response) {

            // Create the response object from a string
            if (is_string($response)) {
                $response = Psr7\parse_response($response);
            } elseif (!($response instanceof ResponseInterface)) {
                throw new \Exception('Responses must be strings or ResponseInterfaces');
            }

            $headers = array_map(function ($h) {
                return implode(' ,', $h);
            }, $response->getHeaders());

            $data[] = [
                'statusCode'   => $response->getStatusCode(),
                'reasonPhrase' => $response->getReasonPhrase(),
                'headers'      => $headers,
                'body'         => (string) $response->getBody(),
            ];
        }

        self::getClient()->put('guzzle-server/responses', [
            'body' => json_encode($data)
        ]);
    }

    /**
     * Get all of the received requests
     *
     * @param bool $hydrate Set to TRUE to turn the messages into
     *                      actual {@see RequestInterface} objects.  If $hydrate is FALSE,
     *                      requests will be returned as strings.
     *
     * @return array
     * @throws \RuntimeException
     */
    public static function received($hydrate = false)
    {
        if (!self::$started) {
            return [];
        }

        $response = self::getClient()->get('guzzle-server/requests');
        $data     = array_filter(explode(self::REQUEST_DELIMITER, (string) $response->getBody()));
        if ($hydrate) {
            $data = array_map(function ($message) {
                return Psr7\parse_request($message);
            }, $data);
        }

        return $data;
    }

    /**
     * Stop running the node.js server
     */
    public static function stop()
    {
        if (self::$started) {
            self::getClient()->delete('guzzle-server');
        }

        self::$started = false;
    }

    public static function wait($maxTries = 3)
    {
        $tries = 0;
        while (!self::isListening() && ++$tries < $maxTries) {
            usleep(100000);
        }

        try {
            self::isListening(false);
        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to contact node.js server: '.$e->getMessage());
        }
    }

    private static function start()
    {
        if (self::$started) {
            return;
        }

        if (!self::isListening()) {
            exec('nodejs '.__DIR__.'/server.js '
                .self::$port.' 1 >> /tmp/server.log 2>&1 &');
            self::wait(10);
        }

        self::$started = true;
    }

    private static function isListening($throw = false)
    {
        try {
            self::getClient()->get('guzzle-server/perf', [
                'connect_timeout' => 5,
                'timeout'         => 5
            ]);

            return true;
        } catch (\Exception $e) {
            if ($throw) {
                throw $e;
            }

            return false;
        }
    }

    private static function getClient()
    {
        if (!self::$client) {
            self::$client = new Client(['base_uri' => self::$url]);
        }

        return self::$client;
    }
}
