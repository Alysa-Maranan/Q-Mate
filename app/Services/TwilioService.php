<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SemaphoreService — replaces Twilio, same interface (sendSMS method)
 * Uses Semaphore PH API: https://semaphore.co
 */
class TwilioService
{
    protected $apiKey;
    protected $senderName;
    protected $apiUrl = 'https://api.semaphore.co/api/v4/messages';

    public function __construct()
    {
        $this->apiKey = env('SEMAPHORE_API_KEY');
        $this->senderName = env('SEMAPHORE_SENDER_NAME', 'SQUIFM');
    }

    /**
     * Send SMS via Semaphore API
     *
     * @param string $to  Phone number (e.g., 09916624892 or +639916624892)
     * @param string $message  Message content
     * @return array
     */
    public function sendSMS($to, $message)
    {
        try {
            if (!$this->apiKey) {
                Log::error('Semaphore API key not configured');
                return ['success' => false, 'message' => 'SMS service not configured'];
            }

            $formattedNumber = $this->formatPhoneNumber($to);

            $response = Http::post($this->apiUrl, [
                'apikey'     => $this->apiKey,
                'number'     => $formattedNumber,
                'message'    => $message,
                'sendername' => $this->senderName,
            ]);

            if ($response->successful()) {
                Log::info('SMS sent via Semaphore', [
                    'to'      => $formattedNumber,
                    'message' => $message,
                ]);
                return ['success' => true, 'message' => 'SMS sent successfully'];
            }

            $error = $response->body();
            Log::error('Semaphore API error', [
                'status' => $response->status(),
                'error'  => $error,
                'to'     => $formattedNumber,
            ]);
            return ['success' => false, 'message' => 'Failed: ' . $error];

        } catch (\Exception $e) {
            Log::error('Semaphore SMS exception', ['error' => $e->getMessage(), 'to' => $to]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Format phone number to 09XXXXXXXXX (Semaphore prefers local format)
     */
    protected function formatPhoneNumber($number)
    {
        $cleaned = preg_replace('/[^0-9+]/', '', $number);

        // +639XXXXXXXXX → 09XXXXXXXXX
        if (preg_match('/^\+639(\d{9})$/', $cleaned, $m)) {
            return '09' . $m[1];
        }

        // Already 09XXXXXXXXX
        if (preg_match('/^09\d{9}$/', $cleaned)) {
            return $cleaned;
        }

        return $cleaned;
    }
}

