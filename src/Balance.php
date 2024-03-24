<?php

namespace Cmercado93\SmsmasivosApiClient;

use Cmercado93\SmsmasivosApiClient\Common\Endpoints;
use Cmercado93\SmsmasivosApiClient\Http\Request;

class Balance
{
    public function get() : int
    {
        $request = new Request;

        $response = $request->post(Endpoints::URI_GET_BALANCE);

        if (is_numeric($response)) {
            return (int) $response;
        }

        return -1;
    }
}
