<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{

    private string $api_key = "c32027ee8c4b5a8f1135bf857b64f308";
    private string $api_key_secret = "0eec8b01cf8ef9e2b73dc4159aab5f6a";

    public function send($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "latchere.y@gmail.com",
                        'Name' => "Mailjet Pilot"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4839133,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}