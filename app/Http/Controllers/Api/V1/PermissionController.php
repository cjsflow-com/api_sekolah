<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permissions\StorePermissionRequest;
use App\Http\Requests\Permissions\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PermissionController extends Controller
{
    /**
     * Daftar permission.
     */
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny',Permission::class);
        $perPage = min(
            max(
                $request->integer(
                    'per_page',
                    20
                ),
                1
            ),
            100
        );

        $permissions = Permission::query()

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
                        function (
                            Builder $query
                        ) use ($search): void {
                            $query
                                ->where(
                                    'module',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'action',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )

            ->when(
                $request->filled('module'),
                fn (Builder $query) =>
                    $query->where(
                        'module',
                        $request->query(
                            'module'
                        )
                    )
            )

            ->orderBy('module')
            ->orderBy('action')

            ->paginate($perPage)
            ->withQueryString();

        return PermissionResource::collection(
            $permissions
        );
    }

    /**
     * Data seluruh permission untuk
     * Checkbox / Tree pada frontend.
     */
    public function options(): JsonResponse
    {
        $permissions = Permission::query()
            ->orderBy('module')
            ->orderBy('action')
            ->get()
            ->groupBy('module')
            ->map(
                function ($permissions, $module) {
                    return [
                        'module' => $module,

                        'permissions' =>
                            $permissions
                                ->map(
                                    fn (
                                        Permission $permission
                                    ) => [
                                        'id' =>
                                            $permission->id,

                                        'name' =>
                                            $permission->name,

                                        'action' =>
                                            $permission->action,

                                        'code' =>
                                            $permission->code,
                                    ]
                                )
                                ->values(),
                    ];
                }
            )
            ->values();

        return response()->json([
            'data' => $permissions,
        ]);
    }

    /**
     * Membuat permission.
     */
    public function store(
        StorePermissionRequest $request
    ): JsonResponse {
        Gate::authorize('create',Permission::class);
        $permission = Permission::create(
            $request->validated()
        );

        return (new PermissionResource(
            $permission
        ))
            ->additional([
                'message' =>
                    'Permission berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Detail permission untuk form edit.
     */
    public function show(
        Permission $permission
    ): PermissionResource {
        Gate::authorize('view',$permission);
        return new PermissionResource(
            $permission
        );
    }

    /**
     * Update permission.
     */
    public function update(
        UpdatePermissionRequest $request,
        Permission $permission
    ): PermissionResource {
        Gate::authorize('update',$permission);
        $permission->update(
            $request->validated()
        );

        return (new PermissionResource(
            $permission->refresh()
        ))->additional([
            'message' =>
                'Permission berhasil diperbarui.',
        ]);
    }

    /**
     * Hapus permission.
     */
    public function destroy(
        Permission $permission
    ): JsonResponse {
        Gate::authorize('delete',$permission);
        if (
            $permission
                ->roles()
                ->exists()
        ) {
            return response()->json([
                'message' =>
                    'Permission tidak dapat dihapus karena masih digunakan oleh role.',
            ], 409);
        }

        $permission->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}