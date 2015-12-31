<?php

namespace Undine\Oxygen\Exception;

class InvalidBodyException extends ProtocolException
{
    const GENERAL_ERROR = 30000;
    const BODY_TOO_LARGE = 30001;
    const RESPONSE_NOT_FOUND = 30002;
    const RESPONSE_INVALID_JSON = 30003;
    const RESPONSE_MALFORMED = 30004;
    const RESPONSE_NOT_AN_ARRAY = 30005;
    const ACTION_RESULT_NOT_ARRAY = 30006;
    const STATE_NOT_ARRAY = 30007;
    const EXCEPTION_NOT_ARRAY = 30008;
    const RESULT_NOT_FOUND = 30009;
    const MALFORMED_EXCEPTION = 30010;
    const STATE_EMPTY = 30011;
    const STATE_MALFORMED = 30012;
}
