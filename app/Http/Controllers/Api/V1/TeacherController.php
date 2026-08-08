<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teachers\StoreTeacherRequest;
use App\Http\Requests\Teachers\UpdateTeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Throwable;

class TeacherController extends Controller
{
    /**
     * Daftar guru.
     */
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        $allowedSorts = [
            'name',
            'email',
            'identity_number',
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

        $teachers = Teacher::query()
            ->search(
                $request->query('search')
            )

            ->when(
                $request->filled('identity_type'),
                fn ($query) => $query->where(
                    'identity_type',
                    $request->query('identity_type')
                )
            )

            ->when(
                $request->filled('gender'),
                fn ($query) => $query->where(
                    'gender',
                    $request->integer('gender')
                )
            )

            ->when(
                $request->has('is_active'),
                fn ($query) => $query->where(
                    'is_active',
                    $request->boolean('is_active')
                )
            )

            ->orderBy(
                $sortBy,
                $sortDirection
            )

            ->paginate($perPage)
            ->withQueryString();

        return TeacherResource::collection(
            $teachers
        );
    }

    /**
     * Membuat guru.
     */
    public function store(
        StoreTeacherRequest $request
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
                        'teachers/avatars',
                        'public'
                    );

                $data['avatar'] = $avatarPath;
            }

            $teacher = Teacher::create($data);
        } catch (Throwable $exception) {
            if ($avatarPath) {
                Storage::disk('public')
                    ->delete($avatarPath);
            }

            throw $exception;
        }

        return (new TeacherResource($teacher))
            ->additional([
                'message' =>
                    'Data guru berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Detail guru.
     */
    public function show(
        Teacher $teacher
    ): TeacherResource {
        return new TeacherResource(
            $teacher
        );
    }

    /**
     * Update guru.
     */
    public function update(
        UpdateTeacherRequest $request,
        Teacher $teacher
    ): TeacherResource {
        $data = $request
            ->safe()
            ->except([
                'avatar',
            ]);

        /**
         * Password kosong tidak boleh
         * menggantikan password lama.
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

        $oldAvatar = $teacher->avatar;
        $newAvatar = null;

        try {
            if ($request->hasFile('avatar')) {
                $newAvatar = $request
                    ->file('avatar')
                    ->store(
                        'teachers/avatars',
                        'public'
                    );

                $data['avatar'] = $newAvatar;
            }

            $teacher->update($data);
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

        return (new TeacherResource(
            $teacher->refresh()
        ))->additional([
            'message' =>
                'Data guru berhasil diperbarui.',
        ]);
    }

    /**
     * Menghapus guru.
     */
    public function destroy(
        Teacher $teacher
    ): JsonResponse {
        $avatar = $teacher->avatar;

        $teacher->delete();

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
