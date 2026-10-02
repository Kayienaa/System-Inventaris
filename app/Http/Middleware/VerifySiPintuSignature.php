<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class VerifySiPintuSignature
{
    /**
     * Handle an incoming request from SiPintu Webhook.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 0. Permissive for GET and HEAD probe requests (health check / ping)
        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            return $next($request);
        }

        // 1. Fail-closed: Jika config client_secret kosong, tolak HTTP 503 Service Unavailable
        $secret = config('services.sipintu.client_secret');
        $legacySecret = config('sipintu.client_secret');

        if (blank($secret) && blank($legacySecret)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'SiPintu client secret is not configured',
            ], 503);
        }

        // 2. Validasi tanda tangan: Periksa header X-SiPintu-Signature
        $signature = $request->header('X-SiPintu-Signature')
            ?? $request->header('X-SIPINTU-SIGNATURE')
            ?? $request->server('HTTP_X_SIPINTU_SIGNATURE');

        if (! is_string($signature) || $signature === '') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid signature',
            ], 401);
        }

        $rawBody = $request->getContent();
        $valid = false;
        if (! blank($secret) && hash_equals(hash_hmac('sha256', $rawBody, $secret), $signature)) {
            $valid = true;
        } elseif (! blank($legacySecret) && hash_equals(hash_hmac('sha256', $rawBody, $legacySecret), $signature)) {
            $valid = true;
        }

        if (! $valid) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid signature',
            ], 401);
        }

        // 3. Proteksi Anti-Replay: Periksa field timestamp atau updated_at dari JSON body (toleransi 300 detik)
        $tolerance = (int) config('services.sipintu.webhook_tolerance', 300);
        $rawTimestamp = $request->input('timestamp')
            ?? $request->input('updated_at')
            ?? $request->input('user.updated_at');

        if ($rawTimestamp !== null) {
            $timestamp = is_numeric($rawTimestamp) ? (int) $rawTimestamp : strtotime((string) $rawTimestamp);
            if ($timestamp === false || abs(time() - $timestamp) > $tolerance) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Payload timestamp expired or invalid',
                ], 401);
            }
        }

        // 4. Deduplikasi Event: Cache lock sipintu:event:{event_id} (atau sha1 raw body jika event_id kosong) durasi toleransi x 2
        $eventId = $request->input('event_id') ?? $request->input('id');
        $cacheKey = ! empty($eventId)
            ? "sipintu:event:{$eventId}"
            : 'sipintu:event:' . sha1($rawBody);

        $ttl = $tolerance * 2;
        if (! Cache::add($cacheKey, true, $ttl)) {
            return response()->json(['status' => 'duplicate']);
        }

        return $next($request);
    }
}
