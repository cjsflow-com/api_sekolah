<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StudentLoginRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentAuthController extends Controller
{
    /**
     * Login student menggunakan NIS.
     */
    public function login(
        StudentLoginRequest $request
    ): JsonResponse {
        $validated = $request->validated();

        $credentials = [
            'nis' => $validated['nis'],
            'password' => $validated['password'],

            /**
             * Hanya student aktif
             * yang boleh login.
             */
            'status' => Student::STATUS_ACTIVE,
        ];

        if (
            ! Auth::guard('student')
                ->attempt($credentials)
        ) {
            return response()->json([
                'message' =>
                    'NIS atau password tidak sesuai.',
            ], 422);
        }

        /**
         * Regenerate session setelah login.
         */
        $request->session()
            ->regenerate();

        $student = Auth::guard('student')
            ->user();

        /**
         * Simpan login terakhir.
         */
        $student->update([
            'last_login_at' => now(),
        ]);

        return response()->json([
            'message' => 'Login berhasil.',

            'data' => [
                'student' => new StudentResource(
                    $student
                ),
            ],
        ]);
    }

    /**
     * Student yang sedang login.
     */
    public function me(
        Request $request
    ): StudentResource {
        $student = Auth::guard('student')
            ->user();

        abort_if(
            ! $student,
            401,
            'Unauthenticated.'
        );

        return new StudentResource(
            $student
        );
    }

    /**
     * Logout student.
     */
    public function logout(
        Request $request
    ): JsonResponse {
        Auth::guard('student')
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
