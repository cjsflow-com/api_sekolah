<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schedules\StoreScheduleRequest;
use App\Http\Requests\Schedules\UpdateScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ScheduleController extends Controller
{
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', Schedule::class);

        $allowedSorts = [
            'day',
            'start_time',
            'end_time',
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

        $schedules = Schedule::query()
            ->with([
                'teachingAssignment.teacher',
                'teachingAssignment.subject',
                'teachingAssignment.schoolClass',
                'teachingAssignment.semester',
                'classRoom',
            ])
            ->when(
                $request->filled('teaching_assignment_id'),
                fn (Builder $query) => $query->where(
                    'teaching_assignment_id',
                    $request->integer('teaching_assignment_id')
                )
            )
            ->when(
                $request->filled('class_room_id'),
                fn (Builder $query) => $query->where(
                    'class_room_id',
                    $request->integer('class_room_id')
                )
            )
            ->when(
                $request->filled('day'),
                fn (Builder $query) => $query->where(
                    'day',
                    (string) $request->query('day')
                )
            )
            ->when(
                $request->filled('teacher_id'),
                function (Builder $query) use ($request): void {
                    $query->whereHas(
                        'teachingAssignment',
                        fn (Builder $query) => $query->where(
                            'teacher_id',
                            $request->integer('teacher_id')
                        )
                    );
                }
            )
            ->when(
                $request->filled('school_class_id'),
                function (Builder $query) use ($request): void {
                    $query->whereHas(
                        'teachingAssignment',
                        fn (Builder $query) => $query->where(
                            'school_class_id',
                            $request->integer('school_class_id')
                        )
                    );
                }
            )
            ->when(
                $request->filled('semester_id'),
                function (Builder $query) use ($request): void {
                    $query->whereHas(
                        'teachingAssignment',
                        fn (Builder $query) => $query->where(
                            'semester_id',
                            $request->integer('semester_id')
                        )
                    );
                }
            )
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return ScheduleResource::collection($schedules);
    }

    public function store(
        StoreScheduleRequest $request
    ): JsonResponse {
        Gate::authorize('create', Schedule::class);

        $schedule = Schedule::create(
            $request->validated()
        );

        $schedule->load([
            'teachingAssignment.teacher',
            'teachingAssignment.subject',
            'teachingAssignment.schoolClass',
            'teachingAssignment.semester',
            'classRoom',
        ]);

        return (new ScheduleResource($schedule))
            ->additional([
                'message' => 'Jadwal berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        Schedule $schedule
    ): ScheduleResource {
        Gate::authorize('view', $schedule);

        $schedule->load([
            'teachingAssignment.teacher',
            'teachingAssignment.subject',
            'teachingAssignment.schoolClass',
            'teachingAssignment.semester',
            'classRoom',
        ]);

        return new ScheduleResource($schedule);
    }

    public function update(
        UpdateScheduleRequest $request,
        Schedule $schedule
    ): ScheduleResource {
        Gate::authorize('update', $schedule);

        $schedule->update(
            $request->validated()
        );

        $schedule->load([
            'teachingAssignment.teacher',
            'teachingAssignment.subject',
            'teachingAssignment.schoolClass',
            'teachingAssignment.semester',
            'classRoom',
        ]);

        return (new ScheduleResource($schedule))
            ->additional([
                'message' => 'Jadwal berhasil diperbarui.',
            ]);
    }

    public function destroy(
        Schedule $schedule
    ): JsonResponse {
        Gate::authorize('delete', $schedule);

        $schedule->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}