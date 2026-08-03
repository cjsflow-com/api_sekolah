<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeachingAssignment extends Model
{
    /**
     * Kolom yang boleh diisi melalui create() dan update().
     */
    protected $fillable = [
        'teacher_id',
        'subject_id',
        'school_class_id',
        'semester_id',
    ];

    /**
     * Konversi tipe data atribut.
     */
    protected function casts(): array
    {
        return [
            'teacher_id' => 'integer',
            'subject_id' => 'integer',
            'school_class_id' => 'integer',
            'semester_id' => 'integer',
        ];
    }

    /**
     * Guru yang mendapatkan penugasan mengajar.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Mata pelajaran yang diajarkan.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Kelas tempat guru mengajar.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Semester penugasan mengajar.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Detail nilai rapor yang menggunakan penugasan ini.
     */
    public function reportCardDetails(): HasMany
    {
        return $this->hasMany(ReportCardDetail::class);
    }
}
