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
     * @return string|null
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string|null $method
     *
     * @return $this
     */
    public function setMethod($method = null)
    {
        // Support automatic default port mapping.
        if (isset(self::$defaultPortMap[$this->method]) && $this->port === self::$defaultPortMap[$this->method]) {
            // The old port number was mapped to the default transfer port number, re-map it here.
            $this->port = $method === null ? null : self::$defaultPortMap[$method];
        }

        $this->method = $method;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     *
     * @return $this
     */
    public function setUsername($username = null)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     *
     * @return $this
     */
    public function setPassword($password = null)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string|null $host
     *
     * @return $this
     */
    public function setHost($host = null)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int|null $port
     *
     * @return $this
     */
    public function setPort($port = null)
    {
        $this->port = $port;

        return $this;
    }
}
