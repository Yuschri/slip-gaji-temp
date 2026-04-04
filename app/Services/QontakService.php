<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QontakService
{
    protected $token;
    protected $templateId;
    protected $channelId;
    protected $baseUrl = 'https://service-chat.qontak.com/api/open/v1';

    public function __construct()
    {
        $this->token = config('services.qontak.token');
        $this->templateId = config('services.qontak.template_id');
        $this->channelId = config('services.qontak.channel_id');
    }

    /**
     * Upload file to Qontak's media storage
     * Returns the URL of the uploaded file
     */
    public function uploadFile($fileBinary, $filename)
    {
        $token = config('services.qontak.token');
        if (!$token) {
            throw new \Exception('Qontak token is not configured in .env');
        }

        $response = Http::withToken($token, 'Bearer')
            ->withoutVerifying()
            ->timeout(120)
            ->attach('file', $fileBinary, $filename)
            ->post($this->baseUrl . '/file_uploader');

        if ($response->failed()) {
            Log::error('Qontak File Upload Failed', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);
            throw new \Exception('Failed to upload file to Qontak: ' . ($response->json()['error']['message'] ?? 'Unknown error'));
        }

        return $response->json()['data']['url'] ?? null;
    }

    /**
     * Format phone number to 628...
     */
    public function formatPhoneNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        } elseif (str_starts_with($number, '8')) {
            $number = '62' . $number;
        }
        return $number;
    }

    /**
     * Send direct broadcast to a single number
     */
    public function sendDirectBroadcast(
        string $phoneNumber,
        string $name,
        array $bodyVariables = [],
        array $header = null
    ) {
        $formattedParameters = [];
        foreach ($bodyVariables as $index => $var) {
            if (is_array($var)) {
                $formattedParameters[] = [
                    'key' => (string) ($index + 1),
                    'value' => $var['key'] ?? $var['label'] ?? 'variable',
                    'value_text' => (string) ($var['value'] ?? $var['value_text'] ?? ''),
                ];
            } else {
                $formattedParameters[] = [
                    'key' => (string) ($index + 1),
                    'value' => 'bulan_tahun',
                    'value_text' => (string) $var,
                ];
            }
        }

        $payload = [
            'to_number' => $this->formatPhoneNumber($phoneNumber),
            'to_name' => $name,
            'message_template_id' => config('services.qontak.template_id'),
            'channel_integration_id' => config('services.qontak.channel_id'),
            'language' => [
                'code' => 'id'
            ],
            'parameters' => [
                'body' => $formattedParameters
            ]
        ];

        if ($header) {
            $payload['parameters']['header'] = $header;
        }

        $response = Http::withToken(config('services.qontak.token'), 'Bearer')
            ->withoutVerifying()
            ->timeout(120)
            ->acceptJson()
            ->post($this->baseUrl . '/broadcasts/whatsapp/direct', $payload);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Qontak Broadcast Error', [
            'status' => $response->status(),
            'body' => $response->json()
        ]);

        throw new \Exception('Qontak Broadcast Failed: ' . ($response->json()['error']['message'] ?? 'Unknown error'));
    }
}
