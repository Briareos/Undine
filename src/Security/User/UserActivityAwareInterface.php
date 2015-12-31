<?php

namespace Undine\Security\User;

interface UserActivityAwareInterface
{
    /**
     * @return \DateTime|null
     */
    public function getLastActiveAt();

    /**
     * @param \DateTime|null $lastActiveAt
     *
     * @return $this
     */
    public function setLastActiveAt(\DateTime $lastActiveAt = null);

    /**
     * @return \DateTime|null
     */
    public function getLastLoginAt();

    /**
     * @param \DateTime|null $lastLoginAt
     *
     * @return $this
     */
    public function setLastLoginAt(\DateTime $lastLoginAt = null);
}
