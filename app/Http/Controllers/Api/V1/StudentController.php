<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Students\StoreStudentRequest;
use App\Http\Requests\Students\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Throwable;

class StudentController extends Controller
{
    /**
     * Daftar siswa.
     */
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        $allowedSorts = [
            'name',
            'nis',
            'nisn',
            'created_at',
            'updated_at',
        ];

        $sortBy = in_array(
            $request->query('sort_by'),
            $allowedSorts,
            true
        )
            ? $request->query('sort_by')
            : 'created_at';

        $sortDirection =
            $request->query('sort_direction') === 'asc'
                ? 'asc'
                : 'desc';

        $perPage = min(
            max(
                $request->integer(
                    'per_page',
                    15
                ),
                1
            ),
            100
        );

        $students = Student::query()

            ->search(
                $request->query('search')
            )

            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request->query('status')
                )
            )

            ->when(
                $request->filled('gender'),
                fn ($query) => $query->where(
                    'gender',
                    $request->integer('gender')
                )
            )

            ->orderBy(
                $sortBy,
                $sortDirection
            )

            ->paginate($perPage)
            ->withQueryString();

        return StudentResource::collection(
            $students
        );
    }

    /**
     * Membuat student baru.
     */
    public function store(
        StoreStudentRequest $request
    ): JsonResponse {
        $data = $request
            ->safe()
            ->except([
                'avatar',
            ]);

        $avatarPath = null;

        try {
            if ($request->hasFile('avatar')) {
                $avatarPath = $request
                    ->file('avatar')
                    ->store(
                        'students/avatars',
                        'public'
                    );

                $data['avatar'] = $avatarPath;
            }

            $student = Student::create(
                $data
            );
        } catch (Throwable $exception) {
            if ($avatarPath) {
                Storage::disk('public')
                    ->delete($avatarPath);
            }

            throw $exception;
        }

        return (new StudentResource($student))
            ->additional([
                'message' =>
                    'Data siswa berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Detail student.
     */
    public function show(
        Student $student
    ): StudentResource {
        return new StudentResource(
            $student
        );
    }

    /**
     * Update student.
     */
    public function update(
        UpdateStudentRequest $request,
        Student $student
    ): StudentResource {
        $data = $request
            ->safe()
            ->except([
                'avatar',
            ]);

        /**
         * Jangan mengganti password lama
         * kalau password kosong.
         */
        if (
            array_key_exists(
                'password',
                $data
            )
            && blank($data['password'])
        ) {
            unset($data['password']);
        }

        $oldAvatar = $student->avatar;
        $newAvatar = null;

        try {
            if ($request->hasFile('avatar')) {
                $newAvatar = $request
                    ->file('avatar')
                    ->store(
                        'students/avatars',
                        'public'
                    );

                $data['avatar'] = $newAvatar;
            }

            $student->update(
                $data
            );
        } catch (Throwable $exception) {
            if ($newAvatar) {
                Storage::disk('public')
                    ->delete($newAvatar);
            }

            throw $exception;
        }

        if (
            $newAvatar
            && $oldAvatar
            && $oldAvatar !== $newAvatar
        ) {
            Storage::disk('public')
                ->delete($oldAvatar);
        }

        return (new StudentResource(
            $student->refresh()
        ))->additional([
            'message' =>
                'Data siswa berhasil diperbarui.',
        ]);
    }

    /**
     * Hapus student.
     */
    public function destroy(
        Student $student
    ): JsonResponse {
        $avatar = $student->avatar;

        $student->delete();

        if ($avatar) {
            Storage::disk('public')
                ->delete($avatar);
        }

        return response()->json(
            data: null,
            status: 204
        );
    }
}
