<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AsmorphicService
{
    protected $baseUrl = 'https://extranet.asmorphic.com';
    protected $bearerToken;

    public function __construct()
    {
        $this->bearerToken = Cache::remember('asmorphic_token', 3600, function () {
            return $this->login();
        });
    }

    protected function login()
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($this->baseUrl . '/api/login', [
                'email' => 'project-test@projecttest.com.au',
                'password' => 'oxhyV9NzkZ^02MEB'
            ]);

            // Log the response for debugging
            Log::info('Asmorphic API Login Response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'json' => $response->json()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Check for token in the correct location (result.token)
                $token = $data['result']['token'] ?? null;

                if ($token) {
                    return $token;
                }

                throw new \Exception('Token not found in response: ' . json_encode($data));
            }

            throw new \Exception('Authentication failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Asmorphic API Login Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Failed to authenticate with Asmorphic API: ' . $e->getMessage());
        }
    }

    protected function ensureAuthenticated()
    {
        if (!$this->bearerToken) {
            Cache::forget('asmorphic_token');
            $this->bearerToken = $this->login();
            Cache::put('asmorphic_token', $this->bearerToken, 3600);
        }
    }

    public function findAddress($address)
    {
        try {
            $this->ensureAuthenticated();
            
            // Parse address string into components
            $addressParts = $this->parseAddress($address);

            // Log the request for debugging
            Log::info('Asmorphic API Find Address Request', [
                'address_parts' => $addressParts,
                'token' => $this->bearerToken
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->bearerToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($this->baseUrl . '/api/orders/findaddress', [
                'company_id' => 17,
                'street_number' => $addressParts['street_number'] ?? null,
                'street_name' => $addressParts['street_name'] ?? '',
                'street_type' => $addressParts['street_type'] ?? '',
                'suburb' => $addressParts['suburb'] ?? '',
                'postcode' => $addressParts['postcode'] ?? '',
                'state' => $addressParts['state'] ?? ''
            ]);

            // Log the response for debugging
            Log::info('Asmorphic API Find Address Response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'json' => $response->json()
            ]);

            if ($response->status() === 401) {
                // Token might be expired, try to refresh it
                Cache::forget('asmorphic_token');
                $this->bearerToken = $this->login();
                Cache::put('asmorphic_token', $this->bearerToken, 3600);
                
                // Retry the request with new token
                return $this->findAddress($address);
            }

            $responseData = $response->json();
            
            // Check if the address was not found
            if ($response->successful() && isset($responseData['success']) && !$responseData['success'] && $responseData['message'] === 'Address not found, please try again.') {
                return [
                    'status' => 'invalid',
                    'message' => 'Address not found in our database. Please check the address and try again.',
                    'raw_response' => $responseData
                ];
            }

            // If the response was successful and address was found
            if ($response->successful() && isset($responseData['success']) && $responseData['success']) {
                return [
                    'status' => 'valid',
                    'message' => 'Address validated successfully',
                    'data' => $responseData['result'] ?? null,
                    'raw_response' => $responseData
                ];
            }

            // For any other error cases
            throw new \Exception($responseData['message'] ?? 'Failed to validate address');
        } catch (\Exception $e) {
            Log::error('Asmorphic API Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'status' => 'error',
                'message' => 'Address validation failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    protected function parseAddress($address)
    {
        // Basic address parsing - you might want to use a more sophisticated parser
        $parts = explode(',', $address);
        $streetParts = explode(' ', trim($parts[0]));
        
        // Attempt to extract street number and name
        $streetNumber = is_numeric($streetParts[0]) ? $streetParts[0] : null;
        $streetName = $streetNumber ? implode(' ', array_slice($streetParts, 1, -1)) : implode(' ', array_slice($streetParts, 0, -1));
        $streetType = end($streetParts);

        return [
            'street_number' => $streetNumber,
            'street_name' => $streetName,
            'street_type' => $streetType,
            'suburb' => trim($parts[1] ?? ''),
            'state' => trim($parts[2] ?? ''),
            'postcode' => trim($parts[3] ?? '')
        ];
    }
} 