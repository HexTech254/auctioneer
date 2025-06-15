<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected $consumer_key;
    protected $consumer_secret;
    protected $passkey;
    protected $shortcode;
    protected $env;
    protected $baseUrl;

    public function __construct()
    {
        $this->consumer_key = config('mpesa.consumer_key');
        $this->consumer_secret = config('mpesa.consumer_secret');
        $this->passkey = config('mpesa.passkey');
        $this->shortcode = config('mpesa.shortcode');
        $this->env = config('mpesa.env');
        $this->baseUrl = 'https://sandbox.safaricom.co.ke';
  }

    protected function getAccessToken()
    {
        try {
            $credentials = base64_encode($this->consumer_key . ':' . $this->consumer_secret);
            
            Log::info('Attempting to get access token with credentials', [
                'consumer_key' => $this->consumer_key,
                'credentials' => $credentials
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $credentials
            ])->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

            Log::info('Token Response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            throw new \Exception('Failed to get access token: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Token Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }


    public function initiateSTKPush($amount, $phone, $reference)
    {
        try {
            $access_token = $this->getAccessToken();
            
            $timestamp = date('YmdHis');
            $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

            Log::info('STK Push Request', [
                'token' => $access_token,
                'phone' => $phone,
                'amount' => $amount
            ]);

            $response = Http::withToken($access_token)
                ->post($this->baseUrl . '/mpesa/stkpush/v1/processrequest', [
                    'BusinessShortCode' => $this->shortcode,
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'TransactionType' => 'CustomerPayBillOnline',
                    'Amount' => $amount,
                    'PartyA' => $phone,
                    'PartyB' => $this->shortcode,
                    'PhoneNumber' => $phone,
                    'CallBackURL' => config('mpesa.callback_url'),
                    'AccountReference' => $reference,
                    'TransactionDesc' => 'Payment for auction'
                ]);

            Log::info('STK Push Response', ['response' => $response->json()]);

            if (!$response->successful()) {
                throw new \Exception($response->body());
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('STK Push Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}