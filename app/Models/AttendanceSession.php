<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'meeting_no',
        'meeting_date',
        'topic',
    ];

    protected function casts(): array
    {
        return [
            'schedule_id' => 'integer',
            'meeting_date' => 'date',
        ];
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
