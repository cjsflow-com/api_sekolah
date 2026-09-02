<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicYears\StoreAcademicYearRequest;
use App\Http\Requests\AcademicYears\UpdateAcademicYearRequest;
use App\Http\Resources\AcademicYearResource;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AcademicYearController extends Controller
{
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', AcademicYear::class);

        $allowedSorts = [
            'name',
            'start_date',
            'end_date',
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

        $academicYears = AcademicYear::query()
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

        return AcademicYearResource::collection(
            $academicYears
        );
    }

    public function store(
        StoreAcademicYearRequest $request
    ): JsonResponse {
        Gate::authorize('create', AcademicYear::class);

        $academicYear = DB::transaction(
            function () use ($request): AcademicYear {
                $data = $request->validated();

                if ($data['is_active'] ?? false) {
                    AcademicYear::query()
                        ->where('is_active', true)
                        ->update([
                            'is_active' => false,
                        ]);
                }

                return AcademicYear::create($data);
            }
        );

        return (new AcademicYearResource($academicYear))
            ->additional([
                'message' => 'Tahun ajaran berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        AcademicYear $academicYear
    ): AcademicYearResource {
        Gate::authorize('view', $academicYear);

        return new AcademicYearResource(
            $academicYear
        );
    }

    public function update(
        UpdateAcademicYearRequest $request,
        AcademicYear $academicYear
    ): AcademicYearResource {
        Gate::authorize('update', $academicYear);

        DB::transaction(
            function () use ($request, $academicYear): void {
                $data = $request->validated();

                if ($data['is_active'] ?? false) {
                    AcademicYear::query()
                        ->whereKeyNot($academicYear->id)
                        ->where('is_active', true)
                        ->update([
                            'is_active' => false,
                        ]);
                }

                $academicYear->update($data);
            }
        );

        return (new AcademicYearResource($academicYear))
            ->additional([
                'message' => 'Tahun ajaran berhasil diperbarui.',
            ]);
    }

    public function destroy(
        AcademicYear $academicYear
    ): JsonResponse {
        Gate::authorize('delete', $academicYear);

        $academicYear->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}