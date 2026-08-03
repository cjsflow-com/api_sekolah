<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    /**
     * Kolom yang boleh diisi menggunakan create() dan update().
     */
    protected $fillable = [
        'academic_year_id',
        'name',
        'level',
        'major',
        'homeroom_teacher_id',
        'capacity',
    ];

    /**
     * Konversi tipe data atribut.
     */
    protected function casts(): array
    {
        return [
            'academic_year_id' => 'integer',
            'level' => 'integer',
            'homeroom_teacher_id' => 'integer',
            'capacity' => 'integer',
        ];
    }

    /**
     * Tahun ajaran tempat kelas ini terdaftar.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Guru yang menjadi wali kelas.
     */
    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(
            Teacher::class,
            'homeroom_teacher_id'
        );
    }

    /**
     * Daftar penugasan mengajar di kelas ini.
     */
    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class);
    }

    /**
     * Daftar rapor yang berasal dari kelas ini.
     */
    public function reportCards(): HasMany
    {
        return $this->hasMany(ReportCard::class);
    }

    /**
     * Filter kelas berdasarkan tingkat.
     */
    public function scopeLevel(
        Builder $query,
        int $level
    ): Builder {
        return $query->where('level', $level);
    }

    /**
     * Filter kelas berdasarkan tahun ajaran.
     */
    public function scopeForAcademicYear(
        Builder $query,
        int $academicYearId
    ): Builder {
        return $query->where(
            'academic_year_id',
            $academicYearId
        );
    }
}
