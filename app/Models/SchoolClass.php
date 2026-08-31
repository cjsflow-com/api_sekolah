<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $fillable = [
        'academic_year_id',
        'education_unit_id',
        'name',
        'level',
        'homeroom_teacher_id',
        'capacity',
    ];

    protected function casts(): array
    {
        return [
            'academic_year_id' => 'integer',
            'education_unit_id' => 'integer',
            'level' => 'integer',
            'homeroom_teacher_id' => 'integer',
            'capacity' => 'integer',
        ];
    }

    /**
     * Tahun ajaran kelas.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Jenjang / satuan pendidikan.
     */
    public function educationUnit(): BelongsTo
    {
        return $this->belongsTo(EducationUnit::class);
    }

    /**
     * Guru wali kelas.
     */
    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(
            Teacher::class,
            'homeroom_teacher_id'
        );
    }

    /**
     * Penugasan mengajar.
     */
    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(
            TeachingAssignment::class
        );
    }

    /**
     * Rapor siswa.
     */
    public function reportCards(): HasMany
    {
        return $this->hasMany(
            ReportCard::class
        );
    }

    /**
     * Filter berdasarkan satuan pendidikan.
     */
    public function scopeForEducationUnit(
        Builder $query,
        int $educationUnitId
    ): Builder {
        return $query->where(
            'education_unit_id',
            $educationUnitId
        );
    }

    /**
     * Filter berdasarkan tingkat kelas.
     */
    public function scopeLevel(
        Builder $query,
        int $level
    ): Builder {
        return $query->where(
            'level',
            $level
        );
    }

    /**
     * Filter berdasarkan tahun ajaran.
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
