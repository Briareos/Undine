<?php

namespace Undine\Event;

/**
 * This class contains the list of all available events in the application.
 * All event classes map to the event names in camel case format (eg. USER_REGISTER => UserRegisterEvent).
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
     * Fires after site.connect API call. The site is persisted to the database right after.
     */
    const SITE_CONNECT = 'site.connect';

    /**
     * Fires after site.disconnect API call. The site is deleted immediately afterwards.
     */
    const SITE_DISCONNECT = 'site.disconnect';

    /**
     * A fresh state has been returned by the site.
     */
    const SITE_STATE_RESULT = 'site.state_result';

    private function __construct()
    {
    }
}
