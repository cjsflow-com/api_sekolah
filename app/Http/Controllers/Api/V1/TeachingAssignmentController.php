<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeachingAssignments\StoreTeachingAssignmentRequest;
use App\Http\Requests\TeachingAssignments\UpdateTeachingAssignmentRequest;
use App\Http\Resources\TeachingAssignmentResource;
use App\Models\TeachingAssignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class TeachingAssignmentController extends Controller
{
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize(
            'viewAny',
            TeachingAssignment::class
        );

        $allowedSorts = [
            'teacher_id',
            'subject_id',
            'school_class_id',
            'semester_id',
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

        $teachingAssignments = TeachingAssignment::query()
            ->with([
                'teacher',
                'subject',
                'schoolClass.educationUnit',
                'schoolClass.academicYear',
                'semester.academicYear',
                'gradeComponents',
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
                                    'teacher',
                                    function (Builder $query) use ($search): void {
                                        $query->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                )
                                ->orWhereHas(
                                    'subject',
                                    function (Builder $query) use ($search): void {
                                        $query
                                            ->where(
                                                'name',
                                                'like',
                                                "%{$search}%"
                                            )
                                            ->orWhere(
                                                'code',
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
                $request->filled('teacher_id'),
                fn (Builder $query) => $query->where(
                    'teacher_id',
                    $request->integer('teacher_id')
                )
            )
            ->when(
                $request->filled('subject_id'),
                fn (Builder $query) => $query->where(
                    'subject_id',
                    $request->integer('subject_id')
                )
            )
            ->when(
                $request->filled('school_class_id'),
                fn (Builder $query) => $query->where(
                    'school_class_id',
                    $request->integer('school_class_id')
                )
            )
            ->when(
                $request->filled('semester_id'),
                fn (Builder $query) => $query->where(
                    'semester_id',
                    $request->integer('semester_id')
                )
            )
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return TeachingAssignmentResource::collection(
            $teachingAssignments
        );
    }

    public function store(
        StoreTeachingAssignmentRequest $request
    ): JsonResponse {
        Gate::authorize(
            'create',
            TeachingAssignment::class
        );

        $teachingAssignment = TeachingAssignment::create(
            $request->validated()
        );

        $teachingAssignment->load([
            'teacher',
            'subject',
            'schoolClass.educationUnit',
            'schoolClass.academicYear',
            'semester.academicYear',
            'gradeComponents',
        ]);

        return (new TeachingAssignmentResource(
            $teachingAssignment
        ))
            ->additional([
                'message' => 'Penugasan mengajar berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        TeachingAssignment $teachingAssignment
    ): TeachingAssignmentResource {
        Gate::authorize(
            'view',
            $teachingAssignment
        );

        $teachingAssignment->load([
            'teacher',
            'subject',
            'schoolClass.educationUnit',
            'schoolClass.academicYear',
            'semester.academicYear',
            'gradeComponents',
        ]);

        return new TeachingAssignmentResource(
            $teachingAssignment
        );
    }

    public function update(
        UpdateTeachingAssignmentRequest $request,
        TeachingAssignment $teachingAssignment
    ): TeachingAssignmentResource {
        Gate::authorize(
            'update',
            $teachingAssignment
        );

        $teachingAssignment->update(
            $request->validated()
        );

        $teachingAssignment->load([
            'teacher',
            'subject',
            'schoolClass.educationUnit',
            'schoolClass.academicYear',
            'semester.academicYear',
            'gradeComponents',
        ]);

        return (new TeachingAssignmentResource(
            $teachingAssignment
        ))
            ->additional([
                'message' => 'Penugasan mengajar berhasil diperbarui.',
            ]);
    }

    public function destroy(
        TeachingAssignment $teachingAssignment
    ): JsonResponse {
        Gate::authorize(
            'delete',
            $teachingAssignment
        );

        $teachingAssignment->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}