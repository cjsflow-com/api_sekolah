<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassAttendance extends Model
{
    //


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

    public function attendanceSession()
    {
        return $this->belongsTo(AttendanceSession::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(Teacher::class, 'recorded_by');
    }
}
