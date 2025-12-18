<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;

class LoggerService
{
    public static function log($action, $description, $status = 'info', $payload = null)
    {
        try {
            ActivityLog::create([
                'action' => $action,
                'description' => $description,
                'status' => $status,
                'payload' => $payload,
            ]);
        } catch (\Exception $e) {
            // Fallback to standard log if DB logging fails
            Log::error("Failed to write to activity log: " . $e->getMessage());
        }
    }
}
