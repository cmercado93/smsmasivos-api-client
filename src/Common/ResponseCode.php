<?php

namespace Cmercado93\SmsmasivosApi\Common;

class ResponseCode
{
    const OK = 0;
    const TEST_OK = 1;
    const LANDLINE = -1;
    const TOO_MANY_FAILURES = -2;
    const UNSUBSCRIBED = -3;
    const DUPLICATE_MESSAGE = -4;
    const SPAM = -5;
    const MESSAGE_TOO_LONG = -6;
    const INVALID_NUMBER_LENGTH = -7;
    const INVALID_NUMBER_CHARS = -8;
    const INVALID_AREA_CODE = -9;
    const CARRIER_REJECTED = -10;
    const INVALID_MESSAGE_CHARS = -11;
    const INVALID_NUMBER_PREFIX = -12;
    const DO_NOT_CALL_LIST = -14;
    const OTHER = -99;
}
