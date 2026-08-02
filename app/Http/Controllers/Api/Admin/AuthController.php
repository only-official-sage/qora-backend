<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminAddress;
use App\Models\AdminPayment;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private JwtService $jwtService;

    public function __construct()
    {
        $this->jwtService = new JwtService;
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $validated['email'])->first();

        if (! $admin || ! Hash::check($validated['password'], $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate JWT
        $token = $this->jwtService->generate($admin);

        // Set httpOnly cookie with JWT
        $minutes = 60 * 24; // 24 hours
        $secure = env('APP_ENV') === 'production'; // secure only in production
        $sameSite = 'strict';
        $cookie = cookie('token', $token, $minutes, '/', null, $secure, true, false, $sameSite);

        return response()->json([
            'user' => $admin,
            'token' => $token, // Add token to response body
        ])->cookie($cookie);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            // Registration
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6',
            // Business Information
            'business_name' => 'required|string',
            'business_type' => 'required|in:Restaurant,Hotel,Restaurant and Hotel',
            'logo' => 'nullable|string', // base64 string
            // Payments
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'account_holder_name' => 'nullable|string',
            // Company Address Information
            'address' => 'nullable|string',
            'country' => 'nullable|string',
            'state' => 'nullable|string',
            'city' => 'nullable|string',
            'country_code' => 'nullable|string',
            'phone_number' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            // Handle logo upload
            $logoPath = null;
            if (! empty($validated['logo'])) {
                // Remove data:image/...;base64, prefix if present
                $base64String = $validated['logo'];
                if (str_contains($base64String, 'base64,')) {
                    $base64String = explode('base64,', $base64String)[1];
                }

                $imageData = base64_decode($base64String);
                $filename = 'logos/'.Str::uuid().'.png'; // Assuming PNG, could detect mime
                // Storage::disk('local')->put($filename, $imageData);
                Storage::disk('public')->put($filename, $imageData);
                $logoPath = $filename;
            }

            // Create admin
            $admin = Admin::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'business_name' => $validated['business_name'],
                'business_type' => $validated['business_type'],
                'logo' => $logoPath,
            ]);

            // Generate JWT
            $token = $this->jwtService->generate($admin);

            // Create address
            AdminAddress::create([
                'admin_id' => $admin->id,
                'address' => $validated['address'] ?? null,
                'country' => $validated['country'] ?? null,
                'state' => $validated['state'] ?? null,
                'city' => $validated['city'] ?? null,
                'country_code' => $validated['country_code'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
            ]);

            // Create payment
            AdminPayment::create([
                'admin_id' => $admin->id,
                'bank_name' => $validated['bank_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'account_holder_name' => $validated['account_holder_name'] ?? null,
            ]);

            // Set httpOnly cookie
            $minutes = 60 * 24;
            $secure = env('APP_ENV') === 'production';
            $sameSite = 'strict';
            $cookie = cookie('token', $token, $minutes, '/', null, $secure, true, false, $sameSite);

            return response()->json([
                'user' => $admin,
                'token' => $token, // Add token to response body
            ], 201)->withCookie($cookie);
        });
    }

    public function checkEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $exists = Admin::where('email', $validated['email'])->exists();

        return response()->json(['exists' => $exists]);
    }

    public function logout(Request $request)
    {
        // Clear the token cookie
        $cookie = cookie('token', '', -60, '/', null, false, true);

        return response()->json(['message' => 'Logged out successfully'])->withCookie($cookie);
    }

    public function profile(Request $request)
    {
        $admin = $request->user();
        $admin->load('address', 'payment');

        return response()->json($admin);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'sometimes|string',
            'business_type' => 'sometimes|in:Restaurant,Hotel,Restaurant and Hotel',
            'logo' => 'sometimes|string',
            'address' => 'sometimes|string',
            'country' => 'sometimes|string',
            'state' => 'sometimes|string',
            'city' => 'sometimes|string',
            'country_code' => 'sometimes|string',
            'phone_number' => 'sometimes|string',
            'bank_name' => 'sometimes|string',
            'account_number' => 'sometimes|string',
            'account_holder_name' => 'sometimes|string',
        ]);

        $admin = $request->user();
        $oldLogo = $admin->logo;
        $newLogoPath = null;

        // Handle logo upload outside transaction (but before DB update)
        if (array_key_exists('logo', $validated)) {
            $base64String = $validated['logo'];
            // Remove data URI prefix if present
            if (str_contains($base64String, 'base64,')) {
                $base64String = explode('base64,', $base64String)[1];
            }
            $imageData = base64_decode($base64String, true);
            if ($imageData === false) {
                return response()->json(['message' => 'Invalid base64 image for logo'], 422);
            }
            // Detect extension
            $extension = 'png';
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_buffer($finfo, $imageData);
                finfo_close($finfo);
                if ($mime === 'image/jpeg') {
                    $extension = 'jpg';
                } elseif ($mime === 'image/png') {
                    $extension = 'png';
                } elseif ($mime === 'image/gif') {
                    $extension = 'gif';
                }
            }
            $filename = 'logos/'.Str::uuid().'.'.$extension;
            //  Storage::disk('local')->put($filename, $imageData);
            Storage::disk('public')->put($filename, $imageData);
            $newLogoPath = $filename;
        }

        $response = DB::transaction(function () use ($validated, $admin, $newLogoPath) {
            // Update admin business fields
            if (array_key_exists('business_name', $validated)) {
                $admin->business_name = $validated['business_name'];
            }
            if (array_key_exists('business_type', $validated)) {
                $admin->business_type = $validated['business_type'];
            }
            if ($newLogoPath !== null) {
                $admin->logo = $newLogoPath;
            }

            $admin->save();

            // Update address
            $address = $admin->address()->firstOrNew([]);
            $addressFields = ['address', 'country', 'state', 'city', 'country_code', 'phone_number'];
            foreach ($addressFields as $field) {
                if (array_key_exists($field, $validated)) {
                    $address->$field = $validated[$field];
                }
            }
            $address->save();

            // Update payment
            $payment = $admin->payment()->firstOrNew([]);
            $paymentFields = ['bank_name', 'account_number', 'account_holder_name'];
            foreach ($paymentFields as $field) {
                if (array_key_exists($field, $validated)) {
                    $payment->$field = $validated[$field];
                }
            }
            $payment->save();

            $admin->load('address', 'payment');

            return response()->json($admin);
        });

        // Delete old logo after successful commit
        if ($newLogoPath !== null && $oldLogo) {
            // Storage::disk('local')->delete($oldLogo);
            Storage::disk('public')->delete($oldLogo);
        }

        return $response;
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $admin = $request->user();

        if (! Hash::check($validated['current_password'], $admin->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $admin->password = Hash::make($validated['new_password']);
        $admin->save();

        return response()->json(['message' => 'Password changed successfully']);
    }
}
