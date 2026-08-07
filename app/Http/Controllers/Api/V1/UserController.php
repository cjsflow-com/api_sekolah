<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UserController extends Controller
{
    /**
     * Daftar user.
     */
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', User::class);

        $allowedSorts = [
            'name',
            'email',
            'created_at',
            'updated_at',
        ];

        $requestedSort = (string) $request->query(
            'sort_by',
            'created_at'
        );

        $sortBy = in_array(
            $requestedSort,
            $allowedSorts,
            true
        )
            ? $requestedSort
            : 'created_at';

        $sortDirection = $request->query(
            'sort_direction'
        ) === 'asc'
            ? 'asc'
            : 'desc';

        $perPage = min(
            max($request->integer('per_page', 15), 1),
            100
        );

        $users = User::query()
            ->with([
                'role.permissions',
            ])

            ->when(
                $request->filled('search'),
                function (Builder $query) use ($request): void {
                    $search = trim(
                        (string) $request->query('search')
                    );

                    $query->where(
                        function (Builder $query) use ($search): void {
                            $query
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )

            ->when(
                $request->filled('role_id'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'role_id',
                        $request->integer('role_id')
                    );
                }
            )

            ->when(
                $request->has('is_active'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'is_active',
                        $request->boolean('is_active')
                    );
                }
            )

            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return UserResource::collection($users);
    }

    /**
     * Membuat user baru.
     */
    public function store(
        StoreUserRequest $request
    ): JsonResponse {
        Gate::authorize('create', User::class);

        $data = $request->safe()->except([
            'avatar',
        ]);

        $avatarPath = null;

        try {
            if ($request->hasFile('avatar')) {
                $avatarPath = $request
                    ->file('avatar')
                    ->store(
                        'users/avatars',
                        'public'
                    );

                $data['avatar'] = $avatarPath;
            }

            $user = User::create($data);
        } catch (Throwable $exception) {
            if ($avatarPath) {
                Storage::disk('public')
                    ->delete($avatarPath);
            }

            throw $exception;
        }

        $user->load([
            'role.permissions',
        ]);

        return (new UserResource($user))
            ->additional([
                'message' => 'User berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Detail user.
     */
    public function show(
        User $user
    ): UserResource {
        Gate::authorize('view', $user);

        $user->load([
            'role.permissions',
        ]);

        return new UserResource($user);
    }

    /**
     * Memperbarui user.
     */
    public function update(
        UpdateUserRequest $request,
        User $user
    ): UserResource {
        Gate::authorize('update', $user);

        $data = $request->safe()->except([
            'avatar',
        ]);

        /*
         * Password kosong tidak boleh menggantikan password lama.
         */
        if (
            array_key_exists('password', $data)
            && blank($data['password'])
        ) {
            unset($data['password']);
        }

        $oldAvatar = $user->avatar;
        $newAvatar = null;

        try {
            if ($request->hasFile('avatar')) {
                $newAvatar = $request
                    ->file('avatar')
                    ->store(
                        'users/avatars',
                        'public'
                    );

                $data['avatar'] = $newAvatar;
            }

            $user->update($data);
        } catch (Throwable $exception) {
            if ($newAvatar) {
                Storage::disk('public')
                    ->delete($newAvatar);
            }

            throw $exception;
        }

        /*
         * Hapus avatar lama setelah update database berhasil.
         */
        if (
            $newAvatar
            && $oldAvatar
            && $oldAvatar !== $newAvatar
        ) {
            Storage::disk('public')
                ->delete($oldAvatar);
        }

        $user->load([
            'role.permissions',
        ]);

        return (new UserResource($user))
            ->additional([
                'message' => 'User berhasil diperbarui.',
            ]);
    }

    /**
     * Menghapus user.
     */
    public function destroy(
        User $user
    ): JsonResponse {
        Gate::authorize('delete', $user);

        $avatar = $user->avatar;

        $user->delete();

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