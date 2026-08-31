<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\EducationUnits\StoreEducationUnitRequest;
use App\Http\Requests\EducationUnits\UpdateEducationUnitRequest;
use App\Http\Resources\EducationUnitResource;
use App\Models\EducationUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class EducationUnitController extends Controller
{
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', EducationUnit::class);

        $allowedSorts = [
            'code',
            'name',
            'is_active',
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

        $educationUnits = EducationUnit::query()
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

        return EducationUnitResource::collection(
            $educationUnits
        );
    }

    public function store(
        StoreEducationUnitRequest $request
    ): JsonResponse {
        Gate::authorize('create', EducationUnit::class);

        $educationUnit = EducationUnit::create(
            $request->validated()
        );

        return (new EducationUnitResource($educationUnit))
            ->additional([
                'message' => 'Satuan pendidikan berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        EducationUnit $educationUnit
    ): EducationUnitResource {
        Gate::authorize('view', $educationUnit);

        return new EducationUnitResource(
            $educationUnit
        );
    }

    public function update(
        UpdateEducationUnitRequest $request,
        EducationUnit $educationUnit
    ): EducationUnitResource {
        Gate::authorize('update', $educationUnit);

        $educationUnit->update(
            $request->validated()
        );

        return (new EducationUnitResource($educationUnit))
            ->additional([
                'message' => 'Satuan pendidikan berhasil diperbarui.',
            ]);
    }

    public function destroy(
        EducationUnit $educationUnit
    ): JsonResponse {
        Gate::authorize('delete', $educationUnit);

        $educationUnit->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}
