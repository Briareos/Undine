<?php

namespace Undine\Model\Site;

class FtpCredentials
{
    const METHOD_FTP = 'ftp';

    const METHOD_SSH = 'ssh';

    private static $defaultPortMap = [
        self::METHOD_FTP => 21,
        self::METHOD_SSH => 22,
    ];

    /**
     * @var string|null
     */
    private $method;

    /**
     * @var string|null
     */
    private $username;

    /**
     * @var string|null
     */
    private $password;

    /**
     * @var string|null
     */
    private $host;

    /**
     * @var int|null
     */
    private $port;

    /**
     * @return null|string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return null|string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return null|string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return null|string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string   $method Transfer method ('ftp' or 'ssh').
     * @param string   $username
     * @param string   $password
     * @param string   $host
     * @param int|null $port   Set to null to use the default port for the transfer method.
     */
    public function set($method, $username, $password, $host = null, $port = null)
    {
        // Since we're relying on this information to be 100% consistent, do additional checks.
        if ($method !== self::METHOD_FTP && $method !== self::METHOD_SSH) {
            throw new \InvalidArgumentException('Method must be either "ftp" or "ssh".');
        }
        if (!strlen($username)) {
            throw new \InvalidArgumentException('Username can not be empty.');
        }
        if ($port !== null && (!ctype_digit((string)$port) || $port < 0 || $port > 0xffff)) {
            throw new \InvalidArgumentException('Port must be a null or a number between 0 and 65535.');
        }

        $this->method   = $method;
        $this->username = (string)$username;
        $this->password = (string)$password;
        $this->host     = strlen($host) ? (string)$host : 'localhost';

        if ($port === null) {
            $this->port = self::$defaultPortMap[$method];
        } else {
            $this->port = (int)$port;
        }
    }

    public function fillWith(FtpCredentials $credentials)
    {
        if ($credentials->present()) {
            $this->set($credentials->getMethod(), $credentials->getUsername(), $credentials->getPassword(), $credentials->getHost(), $credentials->getPort());
        } else {
            $this->clear();
        }
    }

    public function clear()
    {
        $this->method = $this->username = $this->password = $this->host = $this->port = null;
    }

    public function present()
    {
        return $this->method !== null;
    }
}
