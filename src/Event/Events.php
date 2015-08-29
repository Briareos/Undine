<?php

namespace Undine\Event;

/**
 * This class contains the list of all available events in the application.
 */
final class Events
{
    const USER_REGISTER = 'user.register';

    const USER_RESET_PASSWORD = 'user.reset_password';

    const USER_DELETE_ACCOUNT = 'user.delete_account';

    private function __construct()
    {
    }
}
