<?php

namespace Undine\Api\Constraint;

final class SiteConstraint extends AbstractConstraint
{
    const URL_BLANK = 'site.url_blank';
    const URL_INVALID = 'site.url_invalid';
    const URL_TOO_LONG = 'site.url_too_long';
}
