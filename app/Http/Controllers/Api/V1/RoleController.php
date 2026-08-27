<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\StoreRoleRequest;
use App\Http\Requests\Roles\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny',Role::class);
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

        $roles = Role::query()
            ->with([
                'permissions' => function ($query): void {
                    $query
                        ->select([
                            'permissions.id',
                            'permissions.module',
                            'permissions.name',
                            'permissions.action',
                        ])
                        ->orderBy('module')
                        ->orderBy('action');
                },
            ])

            ->withCount([
                'users',
                'permissions',
            ])

            ->when(
                $request->filled('search'),
                function (
                    Builder $query
                ) use ($request): void {
                    $search = trim(
                        (string) $request->query(
                            'search'
                        )
                    );

                    $query->where(
                        'name',
                        'like',
                        "%{$search}%"
                    );
                }
            )

            ->orderBy('name')

            ->paginate($perPage)
            ->withQueryString();

        return RoleResource::collection(
            $roles
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreRoleRequest $request
    ): JsonResponse {
        Gate::authorize('create',Role::class);
        $validated = $request->validated();

        $role = DB::transaction(
            function () use ($validated): Role {
                $role = Role::create([
                    'name' => $validated['name'],
                ]);

                $role->permissions()->sync(
                    $validated['permission_ids']
                        ?? []
                );

                return $role;
            }
        );

        $role->load([
            'permissions',
        ]);

        $role->loadCount([
            'users',
            'permissions',
        ]);

        return (new RoleResource($role))
            ->additional([
                'message' =>
                    'Role berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(
        Role $role
    ): RoleResource {
        Gate::authorize('view',$role);
        $role->load([
            'permissions',
        ]);

        $role->loadCount([
            'users',
            'permissions',
        ]);

        return new RoleResource(
            $role
        );
    }

    /**
     * Update the specified resource in storage.
     */
public function update(
        UpdateRoleRequest $request,
        Role $role
    ): RoleResource {
        Gate::authorize('update',$role);
        $validated = $request->validated();

        DB::transaction(
            function () use (
                $role,
                $validated
            ): void {

                if (
                    array_key_exists(
                        'name',
                        $validated
                    )
                ) {
                    $role->update([
                        'name' =>
                            $validated['name'],
                    ]);
                }

                /**
                 * Permission hanya di-update
                 * kalau permission_ids memang
                 * dikirim frontend.
                 *
                 * Penting untuk PATCH.
                 */
                if (
                    array_key_exists(
                        'permission_ids',
                        $validated
                    )
                ) {
                    $role
                        ->permissions()
                        ->sync(
                            $validated[
                                'permission_ids'
                            ]
                        );
                }
            }
        );

        $role->load([
            'permissions',
        ]);

        $role->loadCount([
            'users',
            'permissions',
        ]);

        return (new RoleResource(
            $role->refresh()
        ))->additional([
            'message' =>
                'Role berhasil diperbarui.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
public function destroy(
        Role $role
    ): JsonResponse {
        Gate::authorize('delete',$role);
        /**
         * Jangan hapus role yang masih
         * digunakan user.
         */
        if ($role->users()->exists()) {
            return response()->json([
                'message' =>
                    'Role tidak dapat dihapus karena masih digunakan oleh user.',
            ], 409);
        }

        DB::transaction(
            function () use ($role): void {
                /**
                 * Hapus relasi permission.
                 */
                $role
                    ->permissions()
                    ->detach();

                /**
                 * Hapus role.
                 */
                $role->delete();
            }
        );

        return response()->json(
            data: null,
            status: 204
        );
    }
}
