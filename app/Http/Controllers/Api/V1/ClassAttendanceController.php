<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassAttendances\UpdateClassAttendanceRequest;
use App\Http\Resources\ClassAttendanceResource;
use App\Models\AttendanceSession;
use App\Models\ClassAttendance;
use App\Models\StudentClass;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ClassAttendanceController extends Controller
{
    public function index(
        AttendanceSession $attendanceSession
    ): AnonymousResourceCollection {
        Gate::authorize(
            'viewAny',
            ClassAttendance::class
        );

        $attendanceSession->loadMissing(
            'schedule.teachingAssignment.schoolClass'
        );

        $schoolClass = $attendanceSession
            ->schedule
            ?->teachingAssignment
            ?->schoolClass;

        abort_if(
            ! $schoolClass,
            422,
            'Kelas dari sesi absensi tidak ditemukan.'
        );

        $studentIds = StudentClass::query()
            ->where(
                'school_class_id',
                $schoolClass->id
            )
            ->where(
                'academic_year_id',
                $schoolClass->academic_year_id
            )
            ->pluck('student_id');

        $attendances = ClassAttendance::query()
            ->with([
                'student',
                'recordedBy',
            ])
            ->where(
                'attendance_session_id',
                $attendanceSession->id
            )
            ->whereIn(
                'student_id',
                $studentIds
            )
            ->orderBy('student_id')
            ->get();

        return ClassAttendanceResource::collection(
            $attendances
        );
    }

    public function update(
        UpdateClassAttendanceRequest $request,
        AttendanceSession $attendanceSession
    ): AnonymousResourceCollection {
        Gate::authorize(
            'update',
            ClassAttendance::class
        );

        /*
         * Sesuaikan bagian ini dengan relasi User -> Teacher
         * pada project kamu.
         */
         $teacher = $request->user('teacher');

        abort_if(
            ! $teacher,
            422,
            'Akun pengguna tidak terhubung dengan data guru.'
        );

        DB::transaction(
            function () use (
                $request,
                $attendanceSession,
                $teacher
            ): void {
                foreach (
                    $request->validated('attendances')
                    as $attendance
                ) {
                    ClassAttendance::query()
                        ->updateOrCreate(
                            [
                                'attendance_session_id' =>
                                    $attendanceSession->id,

                                'student_id' =>
                                    $attendance['student_id'],
                            ],
                            [
                                'status' =>
                                    $attendance['status'],

                                'note' =>
                                    $attendance['note'] ?? null,

                                'recorded_by' =>
                                    $teacher->id,
                            ]
                        );
                }
            }
        );

        $attendances = ClassAttendance::query()
            ->with([
                'student',
                'recordedBy',
            ])
            ->where(
                'attendance_session_id',
                $attendanceSession->id
            )
            ->orderBy('student_id')
            ->get();

        return ClassAttendanceResource::collection(
            $attendances
        )->additional([
            'message' =>
                'Absensi kelas berhasil disimpan.',
        ]);
    }
}