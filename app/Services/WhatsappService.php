<?php

namespace App\Services;
use GuzzleHttp\Client;


final class WhatsappService
{
    protected $client;
    protected $accessToken;
    protected $number;

    public function __construct()
    {
        $this->client = new Client();
        $this->accessToken = config('application.whatsapp.access_token');
        $this->number = config('application.whatsapp.number');
    }

    public function uploadDocument($filePath)
    {

        $url = "https://graph.facebook.com/v20.0/{$this->number}/media";
        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => "Bearer {$this->accessToken}"
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($filePath, 'r'),
                        'filename' => basename($filePath),
                    ],
                    [
                        'name' => 'type',
                        'contents' => 'document',
                    ],

                    [
                        'name' => 'messaging_product',
                        'contents' => 'whatsapp',
                    ],
                ]
            ]);
            return json_decode($response->getBody(), true)['id'];

        } catch (\Exception $e) {
            dd($e->getMessage());
        }


    }


    public function sendDocument($recipientPhone, $mediaId, $caption,$fileName)
    {
        $url = "https://graph.facebook.com/v20.0/{$this->number}/messages";
        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => "Bearer {$this->accessToken}",
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => $recipientPhone,
                    'type' => 'document',
                    'document' => [
                        'id' => $mediaId,
                        'caption' => $caption,
                        'filename' => $fileName,
                    ]
                ]
            ]);
            dd($response->getBody()->getContents());
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

}
