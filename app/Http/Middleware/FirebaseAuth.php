<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * Xác thực Firebase ID Token gửi từ React SPA trên Netlify.
 * Bearer token được lấy từ Firebase Auth (Google Sign-In) phía client.
 */
class FirebaseAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        try {
            // Verify token với Firebase public keys (có cache 1 giờ)
            $payload = $this->verifyFirebaseToken($token);

            if (!$payload) {
                return response()->json(['error' => 'Invalid token.'], 401);
            }

            // Gán UID vào request để các controller dùng
            $request->merge([
                'firebase_uid'   => $payload['sub'],
                'firebase_email' => $payload['email'] ?? null,
                'firebase_name'  => $payload['name'] ?? null,
            ]);

            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token verification failed.'], 401);
        }
    }

    private function verifyFirebaseToken(string $token): ?array
    {
        // Decode header để lấy kid
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;

        $header = json_decode(base64_decode(str_pad(strtr($parts[0], '-_', '+/'), strlen($parts[0]) % 4 == 0 ? strlen($parts[0]) : strlen($parts[0]) + 4 - strlen($parts[0]) % 4, '=')), true);
        $payload = json_decode(base64_decode(str_pad(strtr($parts[1], '-_', '+/'), strlen($parts[1]) % 4 == 0 ? strlen($parts[1]) : strlen($parts[1]) + 4 - strlen($parts[1]) % 4, '=')), true);

        if (!$header || !$payload) return null;

        // Kiểm tra expiry
        if (isset($payload['exp']) && $payload['exp'] < time()) return null;

        // Kiểm tra issuer và audience
        $projectId = config('services.firebase.project_id');
        if ($payload['iss'] !== "https://securetoken.google.com/{$projectId}") return null;
        if ($payload['aud'] !== $projectId) return null;

        // Lấy public keys Firebase (cache 1 giờ)
        $keys = Cache::remember('firebase_public_keys', 3600, function () {
            $response = Http::get('https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com');
            return $response->json();
        });

        $kid = $header['kid'] ?? null;
        if (!$kid || !isset($keys[$kid])) return null;

        // Verify chữ ký RS256
        $publicKey = openssl_pkey_get_public($keys[$kid]);
        if (!$publicKey) return null;

        $data      = $parts[0] . '.' . $parts[1];
        $signature = base64_decode(str_pad(strtr($parts[2], '-_', '+/'), strlen($parts[2]) % 4 == 0 ? strlen($parts[2]) : strlen($parts[2]) + 4 - strlen($parts[2]) % 4, '='));

        $verified = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256);

        return $verified === 1 ? $payload : null;
    }
}
