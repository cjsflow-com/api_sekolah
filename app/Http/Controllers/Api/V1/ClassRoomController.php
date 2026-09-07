<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRooms\StoreClassRoomRequest;
use App\Http\Requests\ClassRooms\UpdateClassRoomRequest;
use App\Http\Resources\ClassRoomResource;
use App\Models\ClassRoom;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ClassRoomController extends Controller
{
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', ClassRoom::class);

        $allowedSorts = [
            'code',
            'name',
            'capacity',
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

        $classRooms = ClassRoom::query()
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
                                    'code',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return ClassRoomResource::collection(
            $classRooms
        );
    }

    public function store(
        StoreClassRoomRequest $request
    ): JsonResponse {
        Gate::authorize('create', ClassRoom::class);

        $classRoom = ClassRoom::create(
            $request->validated()
        );

        return (new ClassRoomResource($classRoom))
            ->additional([
                'message' => 'Ruangan kelas berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        ClassRoom $classRoom
    ): ClassRoomResource {
        Gate::authorize('view', $classRoom);

        return new ClassRoomResource(
            $classRoom
        );
    }

    public function update(
        UpdateClassRoomRequest $request,
        ClassRoom $classRoom
    ): ClassRoomResource {
        Gate::authorize('update', $classRoom);

        $classRoom->update(
            $request->validated()
        );

        return (new ClassRoomResource($classRoom))
            ->additional([
                'message' => 'Ruangan kelas berhasil diperbarui.',
            ]);
    }

    public function destroy(
        ClassRoom $classRoom
    ): JsonResponse {
        Gate::authorize('delete', $classRoom);

        $classRoom->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}