<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ClickSendService
{
    public function sendSMS($to, $message)
    {
        $username = 'info@hurricanehelpflorida.org';
        $apiKey = 'F974EE27-4CEE-213C-524A-6F74F0FA8973';

        $response = Http::withBasicAuth($username, $apiKey)
            ->post('https://rest.clicksend.com/v3/sms/send', [
                'messages' => [
                    [
                        'source' => 'LaravelApp',
                        'body'   => $message,
                        'to'     => $to,
                        'schedule' => null
                    ]
                ]
            ]);

        return $response->json();
    }
}
