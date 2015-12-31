<?php

namespace Undine\Model\Site;

class AdminCredentials
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
     * @param string $username
     * @param string $password
     */
    public function set($username, $password)
    {
        if (!strlen($username)) {
            throw new \InvalidArgumentException('Username must not be an empty string.');
        }
        if (!strlen($password)) {
            throw new \InvalidArgumentException('Password must not be an empty string.');
        }

        $this->username = $username;
        $this->password = $password;
    }

    public function fillWith(AdminCredentials $credentials)
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
