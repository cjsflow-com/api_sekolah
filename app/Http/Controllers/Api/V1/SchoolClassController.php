<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolClasses\StoreSchoolClassRequest;
use App\Http\Requests\SchoolClasses\UpdateSchoolClassRequest;
use App\Http\Resources\SchoolClassResource;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class SchoolClassController extends Controller
{
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', SchoolClass::class);

        $allowedSorts = [
            'name',
            'level',
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

        $schoolClasses = SchoolClass::query()
            ->with([
                'academicYear',
                'educationUnit',
                'homeroomTeacher',
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
                                ->orWhereHas(
                                    'homeroomTeacher',
                                    function (Builder $query) use ($search): void {
                                        $query->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                );
                        }
                    );
                }
            )

            ->when(
                $request->filled('academic_year_id'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'academic_year_id',
                        $request->integer('academic_year_id')
                    );
                }
            )

            ->when(
                $request->filled('education_unit_id'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'education_unit_id',
                        $request->integer('education_unit_id')
                    );
                }
            )

            ->when(
                $request->filled('level'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'level',
                        $request->integer('level')
                    );
                }
            )

            ->when(
                $request->filled('homeroom_teacher_id'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'homeroom_teacher_id',
                        $request->integer('homeroom_teacher_id')
                    );
                }
            )

            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return SchoolClassResource::collection(
            $schoolClasses
        );
    }

    public function store(
        StoreSchoolClassRequest $request
    ): JsonResponse {
        Gate::authorize('create', SchoolClass::class);

        $schoolClass = SchoolClass::create(
            $request->validated()
        );

        $schoolClass->load([
            'academicYear',
            'educationUnit',
            'homeroomTeacher',
        ]);

        return (new SchoolClassResource($schoolClass))
            ->additional([
                'message' => 'Kelas berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        SchoolClass $schoolClass
    ): SchoolClassResource {
        Gate::authorize('view', $schoolClass);

        $schoolClass->load([
            'academicYear',
            'educationUnit',
            'homeroomTeacher',
        ]);

        return new SchoolClassResource($schoolClass);
    }

    public function update(
        UpdateSchoolClassRequest $request,
        SchoolClass $schoolClass
    ): SchoolClassResource {
        Gate::authorize('update', $schoolClass);

        $schoolClass->update(
            $request->validated()
        );

        $schoolClass->load([
            'academicYear',
            'educationUnit',
            'homeroomTeacher',
        ]);

        return (new SchoolClassResource($schoolClass))
            ->additional([
                'message' => 'Kelas berhasil diperbarui.',
            ]);
    }

    public function destroy(
        SchoolClass $schoolClass
    ): JsonResponse {
        Gate::authorize('delete', $schoolClass);

        $schoolClass->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}
