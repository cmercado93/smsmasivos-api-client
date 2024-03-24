<?php

namespace Cmercado93\SmsmasivosApiClient\Common;

class Endpoints
{
    const GENERAL_URL = "https://servicio.smsmasivos.com.ar";

    const URI_SEND_MESSAGE = "enviar_sms.asp";

    const URI_SEND_BULK_MESSAGE = "enviar_sms_bloque.asp";

    const URI_GET_MESSAGES_SENT_IN_BULK = "obtener_respuestaapi_bloque.asp";

    const URI_GET_MESSAGE_RESPONSE = "obtener_sms_entrada.asp";

    const URI_GET_BALANCE = "obtener_saldo.asp";

    const URI_GET_PACKAGE_EXPIRATION = "obtener_vencimiento_paquete.asp";

    const URI_GET_NUMBER_MESSAGES_SENT = "obtener_envios.asp";

    const URI_GET_CURRENT_SERVER_DATE = "get_fecha.asp?iso=1";
}
