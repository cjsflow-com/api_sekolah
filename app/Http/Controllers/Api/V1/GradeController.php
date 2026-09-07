<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Grades\StoreGradeRequest;
use App\Http\Requests\Grades\UpdateGradeRequest;
use App\Http\Resources\GradeResource;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class GradeController extends Controller
{
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', Grade::class);

        $allowedSorts = [
            'student_id',
            'teaching_assignment_id',
            'grade_component_id',
            'score',
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

        $grades = Grade::query()
            ->with([
                'student',
                'teachingAssignment.teacher',
                'teachingAssignment.subject',
                'teachingAssignment.schoolClass',
                'teachingAssignment.semester',
                'gradeComponent',
                'recordedBy',
            ])
            ->when(
                $request->filled('student_id'),
                fn (Builder $query) => $query->where(
                    'student_id',
                    $request->integer('student_id')
                )
            )
            ->when(
                $request->filled('teaching_assignment_id'),
                fn (Builder $query) => $query->where(
                    'teaching_assignment_id',
                    $request->integer('teaching_assignment_id')
                )
            )
            ->when(
                $request->filled('grade_component_id'),
                fn (Builder $query) => $query->where(
                    'grade_component_id',
                    $request->integer('grade_component_id')
                )
            )
            ->when(
                $request->filled('recorded_by'),
                fn (Builder $query) => $query->where(
                    'recorded_by',
                    $request->integer('recorded_by')
                )
            )
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return GradeResource::collection($grades);
    }

    public function store(
        StoreGradeRequest $request
    ): JsonResponse {
        Gate::authorize('create', Grade::class);

        $grade = Grade::create(
            $request->validated()
        );

        $grade->load([
            'student',
            'teachingAssignment.teacher',
            'teachingAssignment.subject',
            'teachingAssignment.schoolClass',
            'teachingAssignment.semester',
            'gradeComponent',
            'recordedBy',
        ]);

        return (new GradeResource($grade))
            ->additional([
                'message' => 'Nilai berhasil disimpan.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        Grade $grade
    ): GradeResource {
        Gate::authorize('view', $grade);

        $grade->load([
            'student',
            'teachingAssignment.teacher',
            'teachingAssignment.subject',
            'teachingAssignment.schoolClass',
            'teachingAssignment.semester',
            'gradeComponent',
            'recordedBy',
        ]);

        return new GradeResource($grade);
    }

    public function update(
        UpdateGradeRequest $request,
        Grade $grade
    ): GradeResource {
        Gate::authorize('update', $grade);

        $grade->update(
            $request->validated()
        );

        $grade->load([
            'student',
            'teachingAssignment.teacher',
            'teachingAssignment.subject',
            'teachingAssignment.schoolClass',
            'teachingAssignment.semester',
            'gradeComponent',
            'recordedBy',
        ]);

        return (new GradeResource($grade))
            ->additional([
                'message' => 'Nilai berhasil diperbarui.',
            ]);
    }

    public function destroy(
        Grade $grade
    ): JsonResponse {
        Gate::authorize('delete', $grade);

        $grade->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}