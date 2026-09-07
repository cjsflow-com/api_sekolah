<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentAttendances\StudentAttendanceSummaryRequest;
use App\Http\Resources\StudentAttendanceResource;
use App\Models\ClassAttendance;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class StudentAttendanceController extends Controller
{
    public function show(
        StudentAttendanceSummaryRequest $request,
        Student $student
    ): JsonResponse {
        $perPage = min(
            max($request->integer('per_page', 15), 1),
            100
        );

        $query = ClassAttendance::query()
            ->where(
                'student_id',
                $student->id
            )
            ->when(
                $request->filled('semester_id'),
                function (
                    Builder $query
                ) use ($request): void {
                    $query->whereHas(
                        'attendanceSession.schedule.teachingAssignment',
                        fn (Builder $query) => $query->where(
                            'semester_id',
                            $request->integer('semester_id')
                        )
                    );
                }
            )
            ->when(
                $request->filled('academic_year_id'),
                function (
                    Builder $query
                ) use ($request): void {
                    $query->whereHas(
                        'attendanceSession.schedule.teachingAssignment.semester',
                        fn (Builder $query) => $query->where(
                            'academic_year_id',
                            $request->integer(
                                'academic_year_id'
                            )
                        )
                    );
                }
            )
            ->when(
                $request->filled('subject_id'),
                function (
                    Builder $query
                ) use ($request): void {
                    $query->whereHas(
                        'attendanceSession.schedule.teachingAssignment',
                        fn (Builder $query) => $query->where(
                            'subject_id',
                            $request->integer('subject_id')
                        )
                    );
                }
            );

        /*
        |--------------------------------------------------------------------------
        | Rekap absensi
        |--------------------------------------------------------------------------
        |
        | Query ini mengikuti filter semester, tahun ajaran,
        | dan mata pelajaran.
        |
        */

        $summary = (clone $query)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as present',
                [ClassAttendance::STATUS_PRESENT]
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as sick',
                [ClassAttendance::STATUS_SICK]
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as excused',
                [ClassAttendance::STATUS_EXCUSED]
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as absent',
                [ClassAttendance::STATUS_ABSENT]
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as late',
                [ClassAttendance::STATUS_LATE]
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Riwayat absensi
        |--------------------------------------------------------------------------
        */

        $attendances = (clone $query)
            ->with([
                'attendanceSession.schedule.teachingAssignment.subject',
                'attendanceSession.schedule.teachingAssignment.teacher',
                'attendanceSession.schedule.teachingAssignment.schoolClass',
                'attendanceSession.schedule.teachingAssignment.semester.academicYear',
                'recordedBy',
            ])
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,

                    'nis' =>
                        $student->nis ?? null,

                    'nisn' =>
                        $student->nisn ?? null,
                ],

                'filters' => [
                    'academic_year_id' =>
                        $request->integer(
                            'academic_year_id'
                        ) ?: null,

                    'semester_id' =>
                        $request->integer(
                            'semester_id'
                        ) ?: null,

                    'subject_id' =>
                        $request->integer(
                            'subject_id'
                        ) ?: null,
                ],

                'summary' => [
                    'total' =>
                        (int) ($summary->total ?? 0),

                    'present' =>
                        (int) ($summary->present ?? 0),

                    'sick' =>
                        (int) ($summary->sick ?? 0),

                    'excused' =>
                        (int) ($summary->excused ?? 0),

                    'absent' =>
                        (int) ($summary->absent ?? 0),

                    'late' =>
                        (int) ($summary->late ?? 0),
                ],

                'attendances' =>
                    StudentAttendanceResource::collection(
                        $attendances
                    )->response()->getData(true),
            ],
        ]);
    }
}