<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //

     public function login(
        LoginRequest $request
    ): JsonResponse {
        $credentials = $request->validated();

        $user = User::query()
            ->where('email', $credentials['email'])
            ->first();

        if (
            ! $user
            || ! Hash::check(
                $credentials['password'],
                $user->password
            )
        ) {
            return response()->json([
                'message' => 'Email atau password tidak sesuai.',
            ], 422);
        }

        if (! $user->is_active) {
            return response()->json([
                'message' => 'Akun Anda sedang tidak aktif.',
            ], 403);
        }

        $deviceName = $credentials['device_name']
            ?? 'api-client';

        $token = $user->createToken(
            name: $deviceName,
            abilities: ['*']
        )->plainTextToken;

        $user->load([
            'role.permissions',
        ]);

        return response()->json([
            'message' => 'Login berhasil.',

            'data' => [
                'user' => new UserResource($user),

                'auth' => [
                    'token_type' => 'Bearer',
                    'access_token' => $token,
                ],
            ],
        ]);
    }

    // Mengambil user yang sedang login
    public function me(Request $request): UserResource{
        $user = $request->user();
        $user->load([
            'role.permissions',
        ]);
        return new UserResource($user);
    }

    // Menghapus token akses saat logout
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->delete();

        return response()->json([
            'message' => 'Logout dari semua perangkat berhasil.',
        ]);
    }
}
