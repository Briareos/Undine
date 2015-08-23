<?php

namespace Undine\Api\Constraint;

final class SecurityConstraint extends AbstractConstraint
{
    const NOT_AUTHENTICATED = 'security.not_authenticated';

    const NOT_AUTHORIZED = 'security.not_authorized';

    const BAD_CREDENTIALS = 'security.bad_credentials';
}
