<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassAttendance extends Model
{
    protected $fillable = [
        'attendance_session_id',
        'student_id',
        'status',
        'note',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'attendance_session_id' => 'integer',
            'student_id' => 'integer',
            'recorded_by' => 'integer',
        ];
    }

    public function attendanceSession(): BelongsTo
    {
        return $this->belongsTo(
            AttendanceSession::class
        );
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(
            Student::class
        );
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(
            Teacher::class,
            'recorded_by'
        );
    }
}