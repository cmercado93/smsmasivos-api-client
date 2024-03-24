<?php

namespace Cmercado93\SmsmasivosApiClient;

use Cmercado93\SmsmasivosApiClient\Common\Endpoints;
use Cmercado93\SmsmasivosApiClient\Http\Request;
use DateTime;

class Package
{
    public function get() : ?DateTime
    {
        $request = new Request;

        $response = $request->post(Endpoints::URI_GET_PACKAGE_EXPIRATION);

        if (preg_match('/^20[0-9]{2}\-[0-1][0-2]\-[0-3][0-9]$/', $response)) {
            return new DateTime($response);
        }

        return null;
    }
}
