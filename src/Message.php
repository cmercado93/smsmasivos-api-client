<?php

namespace Cmercado93\SmsmasivosApiClient;

class Message
{
    protected $data = [
        'phone' => null,
        'text' => null,
        'internal_id' => null,
        'html' => null,
        'shipping_date' => null,
        'status' => null,
    ];

    public function __construct(array $data)
    {
        $this->data['phone'] = $data['phone'] ?? null;

        $this->data['text'] = $data['text'] ?? null;

        $this->data['internal_id'] = $data['internal_id'] ?? null;

        $this->data['html'] = $data['html'] ?? null;

        $this->data['shipping_date'] = $data['shipping_date'] ?? null;

        $this->data['status'] = $data['status'] ?? null;
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }
}
