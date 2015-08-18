<?php

namespace Undine\Api\Constraint;

final class SecurityConstraint
{
    const NOT_AUTHENTICATED = 'security.not_authenticated';

    const NOT_AUTHORIZED = 'security.not_authorized';

    const BAD_CREDENTIALS = 'security.bad_credentials';

    private function __construct()
    {
    }
}
