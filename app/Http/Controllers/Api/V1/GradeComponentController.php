<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeComponents\StoreGradeComponentRequest;
use App\Http\Requests\GradeComponents\UpdateGradeComponentRequest;
use App\Http\Resources\GradeComponentResource;
use App\Models\GradeComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class GradeComponentController extends Controller
{
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', GradeComponent::class);

        $allowedSorts = [
            'name',
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

        $gradeComponents = GradeComponent::query()
            ->when(
                $request->filled('search'),
                function (Builder $query) use ($request): void {
                    $search = trim(
                        (string) $request->query('search')
                    );

                    $query->where(
                        'name',
                        'like',
                        "%{$search}%"
                    );
                }
            )
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return GradeComponentResource::collection(
            $gradeComponents
        );
    }

    public function store(
        StoreGradeComponentRequest $request
    ): JsonResponse {
        Gate::authorize('create', GradeComponent::class);

        $gradeComponent = GradeComponent::create(
            $request->validated()
        );

        return (new GradeComponentResource($gradeComponent))
            ->additional([
                'message' => 'Komponen nilai berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        GradeComponent $gradeComponent
    ): GradeComponentResource {
        Gate::authorize('view', $gradeComponent);

        return new GradeComponentResource(
            $gradeComponent
        );
    }

    public function update(
        UpdateGradeComponentRequest $request,
        GradeComponent $gradeComponent
    ): GradeComponentResource {
        Gate::authorize('update', $gradeComponent);

        $gradeComponent->update(
            $request->validated()
        );

        return (new GradeComponentResource($gradeComponent))
            ->additional([
                'message' => 'Komponen nilai berhasil diperbarui.',
            ]);
    }

    public function destroy(
        GradeComponent $gradeComponent
    ): JsonResponse {
        Gate::authorize('delete', $gradeComponent);

        $gradeComponent->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}