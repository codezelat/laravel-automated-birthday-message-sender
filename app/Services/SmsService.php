<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $username;
    protected $password;
    protected $source;
    protected $apiUrl;

    public function __construct()
    {
        $this->username = env('SMS_USERNAME');
        $this->password = env('SMS_PASSWORD');
        $this->source = env('SMS_SOURCE');
        $this->apiUrl = env('SMS_API_URL');
    }

    public function send($phone, $message)
    {
        try {
            // Assuming standard parameters for the provided endpoint style.
            // Adjust parameters keys based on specific API documentation if available.
            // Common keys: user, pass, src, dst, msg OR username, password, source, dest, message
            
            // Using logic derived from env variable naming convention
            $response = Http::get($this->apiUrl, [
                'username' => $this->username,
                'password' => $this->password,
                'src' => $this->source,
                'dst' => $phone,
                'msg' => $message,
                'dr' => 1, // Delivery Report often optional
            ]);

            if ($response->successful()) {
                LoggerService::log('SMS Sent', "SMS sent to {$phone}", 'success', ['response' => $response->body()]);
                Log::info("SMS sent to {$phone}: {$response->body()}");
                return true;
            } else {
                LoggerService::log('SMS Failed', "SMS failed to {$phone}", 'error', ['status' => $response->status(), 'response' => $response->body()]);
                Log::error("SMS failed to {$phone}: {$response->status()} - {$response->body()}");
                return false;
            }
        } catch (\Exception $e) {
            LoggerService::log('SMS Error', "Exception sending SMS to {$phone}", 'error', ['message' => $e->getMessage()]);
            Log::error("SMS Exception to {$phone}: " . $e->getMessage());
            return false;
        }
    }
}
