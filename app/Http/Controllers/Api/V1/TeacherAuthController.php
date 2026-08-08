<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TeacherLoginRequest;
use App\Http\Resources\TeacherResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherAuthController extends Controller
{
    /**
     * Login teacher menggunakan identity_number.
     */
    public function login(
        TeacherLoginRequest $request
    ): JsonResponse {
        $validated = $request->validated();

        $credentials = [
            'identity_number' => $validated['identity_number'],
            'password' => $validated['password'],
            'is_active' => true,
        ];

        if (
            ! Auth::guard('teacher')
                ->attempt($credentials)
        ) {
            return response()->json([
                'message' =>
                    'Nomor identitas atau password tidak sesuai.',
            ], 422);
        }

        /**
         * Regenerate session setelah login berhasil.
         */
        $request->session()->regenerate();

        $teacher = Auth::guard('teacher')
            ->user();

        /**
         * Update waktu login terakhir.
         */
        $teacher->update([
            'last_login_at' => now(),
        ]);

        return response()->json([
            'message' => 'Login berhasil.',

            'data' => [
                'teacher' => new TeacherResource(
                    $teacher
                ),
            ],
        ]);
    }

    /**
     * Teacher yang sedang login.
     */
    public function me(
        Request $request
    ): TeacherResource {
        $teacher = Auth::guard('teacher')
            ->user();

        abort_if(
            ! $teacher,
            401,
            'Unauthenticated.'
        );

        return new TeacherResource(
            $teacher
        );
    }

    /**
     * Logout teacher.
     */
    public function logout(
        Request $request
    ): JsonResponse {
        Auth::guard('teacher')
            ->logout();

        $request->session()
            ->invalidate();

        $request->session()
            ->regenerateToken();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }
}
