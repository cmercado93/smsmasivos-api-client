<?php

namespace Cmercado93\SmsmasivosApiClient;

use Cmercado93\SmsmasivosApiClient\Common\Endpoints;
use Cmercado93\SmsmasivosApiClient\Http\Request;
use DateTime;

class CurrentServerDate
{
    public function get() : DateTime
    {
        $request = new Request;

        $response = $request->post(Endpoints::URI_GET_CURRENT_SERVER_DATE);

        return new DateTime($response);
    }
}
