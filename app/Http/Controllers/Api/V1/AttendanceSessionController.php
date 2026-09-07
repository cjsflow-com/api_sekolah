<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceSessions\StoreAttendanceSessionRequest;
use App\Http\Requests\AttendanceSessions\UpdateAttendanceSessionRequest;
use App\Http\Resources\AttendanceSessionResource;
use App\Models\AttendanceSession;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class AttendanceSessionController extends Controller
{
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize(
            'viewAny',
            AttendanceSession::class
        );

        $allowedSorts = [
            'meeting_no',
            'meeting_date',
            'created_at',
            'updated_at',
        ];

        $requestedSort = (string) $request->query(
            'sort_by',
            'meeting_date'
        );

        $sortBy = in_array(
            $requestedSort,
            $allowedSorts,
            true
        )
            ? $requestedSort
            : 'meeting_date';

        $sortDirection = $request->query(
            'sort_direction'
        ) === 'asc'
            ? 'asc'
            : 'desc';

        $perPage = min(
            max($request->integer('per_page', 15), 1),
            100
        );

        $sessions = AttendanceSession::query()
            ->with([
                'schedule.classRoom',
                'schedule.teachingAssignment.teacher',
                'schedule.teachingAssignment.subject',
                'schedule.teachingAssignment.schoolClass',
                'schedule.teachingAssignment.semester',
            ])
            ->when(
                $request->filled('schedule_id'),
                fn (Builder $query) => $query->where(
                    'schedule_id',
                    $request->integer('schedule_id')
                )
            )
            ->when(
                $request->filled('meeting_date'),
                fn (Builder $query) => $query->whereDate(
                    'meeting_date',
                    (string) $request->query('meeting_date')
                )
            )
            ->when(
                $request->filled('search'),
                function (Builder $query) use ($request): void {
                    $search = trim(
                        (string) $request->query('search')
                    );

                    $query->where(
                        'topic',
                        'like',
                        "%{$search}%"
                    );
                }
            )
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return AttendanceSessionResource::collection(
            $sessions
        );
    }

    public function store(
        StoreAttendanceSessionRequest $request
    ): JsonResponse {
        Gate::authorize(
            'create',
            AttendanceSession::class
        );

        $session = AttendanceSession::create(
            $request->validated()
        );

        $session->load([
            'schedule.classRoom',
            'schedule.teachingAssignment.teacher',
            'schedule.teachingAssignment.subject',
            'schedule.teachingAssignment.schoolClass',
            'schedule.teachingAssignment.semester',
        ]);

        return (new AttendanceSessionResource($session))
            ->additional([
                'message' => 'Sesi absensi berhasil dibuat.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        AttendanceSession $attendanceSession
    ): AttendanceSessionResource {
        Gate::authorize(
            'view',
            $attendanceSession
        );

        $attendanceSession->load([
            'schedule.classRoom',
            'schedule.teachingAssignment.teacher',
            'schedule.teachingAssignment.subject',
            'schedule.teachingAssignment.schoolClass',
            'schedule.teachingAssignment.semester',
            'classAttendances.student',
            'classAttendances.recordedBy',
        ]);

        return new AttendanceSessionResource(
            $attendanceSession
        );
    }

    public function update(
        UpdateAttendanceSessionRequest $request,
        AttendanceSession $attendanceSession
    ): AttendanceSessionResource {
        Gate::authorize(
            'update',
            $attendanceSession
        );

        $attendanceSession->update(
            $request->validated()
        );

        $attendanceSession->load([
            'schedule.classRoom',
            'schedule.teachingAssignment.teacher',
            'schedule.teachingAssignment.subject',
            'schedule.teachingAssignment.schoolClass',
            'schedule.teachingAssignment.semester',
        ]);

        return (new AttendanceSessionResource(
            $attendanceSession
        ))
            ->additional([
                'message' => 'Sesi absensi berhasil diperbarui.',
            ]);
    }

    public function destroy(
        AttendanceSession $attendanceSession
    ): JsonResponse {
        Gate::authorize(
            'delete',
            $attendanceSession
        );

        $attendanceSession->delete();

        return response()->json(
            data: null,
            status: 204
        );
    }
}
