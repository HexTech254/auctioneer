<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateMpesaIP
{
    protected $allowedIPs = [
        '196.201.214.200',
        '196.201.214.206',
        '196.201.213.114',
        '196.201.214.207',
        '196.201.214.208',
        '196.201.213.44',
        '196.201.212.127',
        '196.201.212.138',
        '196.201.212.129',
        '196.201.212.136',
        '196.201.212.74',
        '196.201.212.69'
    ];

    public function handle(Request $request, Closure $next)
    {
        $clientIP = $request->ip();
        
        // Allow localhost and ngrok for testing
        if (app()->environment('local') || str_contains($clientIP, '127.0.0.1') || str_contains($request->header('User-Agent'), 'ngrok')) {
            return $next($request);
        }

        if (!in_array($clientIP, $this->allowedIPs)) {
            \Log::warning('Unauthorized Mpesa callback attempt', [
                'ip' => $clientIP,
                'headers' => $request->headers->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized IP'
            ], 403);
        }

        return $next($request);
    }
} 