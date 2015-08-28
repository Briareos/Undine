<?php

namespace Undine\Event;

/**
 * This class contains the list of all available events in the application.
 */
final class Events
{
    const USER_REGISTER = 'user.register';

    const USER_RESET_PASSWORD = 'user.resetPassword';

    private function __construct()
    {
    }
}
