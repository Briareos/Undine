<?php

namespace Undine\Event;

/**
 * This class contains the list of all available events in the application.
 */
final class Events
{
    /**
     * A user successfully registered.
     */
    const USER_REGISTER = 'user.register';

    /**
     * The user successfully reset their password.
     */
    const USER_RESET_PASSWORD = 'user.reset_password';

    /**
     * An attacker tried to brute-force the password reset token.
     */
    const USER_RESET_PASSWORD_FAILED = 'user.reset_password_failed';

    /**
     * User deleted their own account.
     */
    const USER_DELETE_ACCOUNT = 'user.delete_account';

    /**
     * Fires after site.disconnect API call. The site is deleted immediately afterwards.
     */
    const SITE_DISCONNECT = 'site.disconnect';

    private function __construct()
    {
    }
}
