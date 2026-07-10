<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use App\Models\Admin;

class JwtService
{
    private string $secret;
    private int $ttl;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET', 'your-secret-key-change-in-production');
        $this->ttl = (int) env('JWT_TTL', 86400); // seconds, 24 hours default
    }

    public function generate(Admin $admin): string
    {
        $now = time();
        $payload = [
            'iss' => 'qora-backend',
            'sub' => $admin->id,
            'email' => $admin->email,
            'iat' => $now,
            'exp' => $now + $this->ttl,
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function validate(string $token): ?Admin
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            $admin = Admin::find($decoded->sub);
            return $admin ?: null;
        } catch (ExpiredException $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
