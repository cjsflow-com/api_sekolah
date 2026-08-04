<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    public const DAY_MONDAY = 'Senin';
    public const DAY_TUESDAY = 'Selasa';
    public const DAY_WEDNESDAY = 'Rabu';
    public const DAY_THURSDAY = 'Kamis';
    public const DAY_FRIDAY = 'Jumat';
    public const DAY_SATURDAY = 'Sabtu';

    /**
     * Kolom yang boleh diisi melalui create() dan update().
     */
    protected $fillable = [
        'teaching_assignment_id',
        'class_room_id',
        'day',
        'start_time',
        'end_time',
    ];

    /**
     * Konversi tipe data atribut.
     */
    protected function casts(): array
    {
        return [
            'teaching_assignment_id' => 'integer',
            'class_room_id' => 'integer',
        ];
    }

    /**
     * Penugasan mengajar yang menggunakan jadwal ini.
     */
    public function teachingAssignment(): BelongsTo
    {
        return $this->belongsTo(TeachingAssignment::class);
    }

    /**
     * Ruangan yang digunakan untuk jadwal ini.
     */
    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class);
    }

    /**
     * Daftar sesi absensi dalam jadwal ini.
     */
    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }

    /**
     * Filter jadwal berdasarkan hari.
     */
    public function scopeForDay(
        Builder $query,
        string $day
    ): Builder {
        return $query->where('day', $day);
    }

    /**
     * Mengurutkan jadwal berdasarkan waktu mulai.
     */
    public function scopeOrderByStartTime(
        Builder $query
    ): Builder {
        return $query->orderBy('start_time');
    }
}
