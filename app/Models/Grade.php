<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    /**
     * Kolom yang boleh diisi melalui create() dan update().
     */
    protected $fillable = [
        'student_id',
        'teaching_assignment_id',
        'grade_component_id',
        'score',
        'recorded_by',
    ];

    /**
     * Konversi tipe data atribut.
     */
    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'teaching_assignment_id' => 'integer',
            'grade_component_id' => 'integer',
            'score' => 'decimal:2',
            'recorded_by' => 'integer',
        ];
    }

    /**
     * Siswa yang mendapatkan nilai.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Penugasan mengajar yang berkaitan dengan nilai.
     */
    public function teachingAssignment(): BelongsTo
    {
        return $this->belongsTo(TeachingAssignment::class);
    }

    /**
     * Komponen nilai yang digunakan.
     */
    public function gradeComponent(): BelongsTo
    {
        return $this->belongsTo(GradeComponent::class);
    }

    /**
     * Guru yang mencatat nilai.
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(
            Teacher::class,
            'recorded_by'
        );
    }
}
