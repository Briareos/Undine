<?php

namespace Undine\Model\Site;

class HttpCredentials
{
    /**
     * @var string|null
     */
    private $username;

    /**
     * @var string|null
     */
    private $password;

    /**
     * @return string|null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Clears the credentials.
     */
    public function clear()
    {
        $this->username = $this->password = null;
    }

    /**
     * @param string      $username
     * @param string|null $password
     */
    public function set($username, $password = null)
    {
        if (!strlen($username)) {
            throw new \InvalidArgumentException('HTTP username must not be an empty string.');
        }

        $this->username = $username;
        $this->password = $password;
    }

    public function fillWith(HttpCredentials $credentials)
    {
        if ($credentials->present()) {
            $this->set($credentials->getUsername(), $credentials->getPassword());
        } else {
            $this->clear();
        }
    }

    /**
     * @return bool
     */
    public function present()
    {
        return $this->username !== null;
    }
}
