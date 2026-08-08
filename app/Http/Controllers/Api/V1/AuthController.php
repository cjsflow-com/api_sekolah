<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    //
     public function login(
        LoginRequest $request
    ): JsonResponse {
        $validated = $request->validated();

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_active' => true,
        ];

        if (! Auth::guard('web')->attempt($credentials)) {
            return response()->json([
                'message' => 'Email atau password salah.',
            ], 422);
        }

        $request->session()->regenerate();

        $user = Auth::guard('web')->user();

        $user->update([
            'last_login_at' => now(),
        ]);

        $user->load([
            'role.permissions',
        ]);

        return response()->json([
            'message' => 'Login berhasil.',
            'data' => [
                'user' => new UserResource($user),
            ]
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
