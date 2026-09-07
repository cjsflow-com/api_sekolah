<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentClasses\StoreStudentClassRequest;
use App\Http\Requests\StudentClasses\UpdateStudentClassRequest;
use App\Http\Resources\StudentClassResource;
use App\Models\StudentClass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class StudentClassController extends Controller
{
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', StudentClass::class);

        $allowedSorts = [
            'student_id',
            'school_class_id',
            'academic_year_id',
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

        $studentClasses = StudentClass::query()
            ->with([
                'student',
                'schoolClass',
                'academicYear',
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
                                ->whereHas(
                                    'student',
                                    function (Builder $query) use ($search): void {
                                        $query->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                )
                                ->orWhereHas(
                                    'schoolClass',
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
                $request->filled('student_id'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'student_id',
                        $request->integer('student_id')
                    );
                }
            )
            ->when(
                $request->filled('school_class_id'),
                function (Builder $query) use ($request): void {
                    $query->where(
                        'school_class_id',
                        $request->integer('school_class_id')
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
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return StudentClassResource::collection(
            $studentClasses
        );
    }

    public function store(
        StoreStudentClassRequest $request
    ): JsonResponse {
        Gate::authorize('create', StudentClass::class);

        $studentClass = StudentClass::create(
            $request->validated()
        );

        $studentClass->load([
            'student',
            'schoolClass',
            'academicYear',
        ]);

        return (new StudentClassResource($studentClass))
            ->additional([
                'message' => 'Penempatan siswa ke kelas berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        StudentClass $studentClass
    ): StudentClassResource {
        Gate::authorize('view', $studentClass);

        $studentClass->load([
            'student',
            'schoolClass',
            'academicYear',
        ]);

        return new StudentClassResource(
            $studentClass
        );
    }

    public function update(
        UpdateStudentClassRequest $request,
        StudentClass $studentClass
    ): StudentClassResource {
        Gate::authorize('update', $studentClass);

        $studentClass->update(
            $request->validated()
        );

        $studentClass->load([
            'student',
            'schoolClass',
            'academicYear',
        ]);

        return (new StudentClassResource($studentClass))
            ->additional([
                'message' => 'Penempatan siswa berhasil diperbarui.',
            ]);
    }

    public function destroy(
        StudentClass $studentClass
    ): JsonResponse {
        Gate::authorize('delete', $studentClass);

        $studentClass->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}