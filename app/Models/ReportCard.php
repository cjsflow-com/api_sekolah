<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportCard extends Model
{
    /**
     * Kolom yang boleh diisi melalui create() dan update().
     */
    protected $fillable = [
        'student_id',
        'semester_id',
        'school_class_id',
        'average_score',
        'attendance_present',
        'attendance_sick',
        'attendance_permission',
        'attendance_absent',
        'teacher_notes',
        'principal_notes',
    ];

    /**
     * Konversi tipe data atribut.
     */
    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'semester_id' => 'integer',
            'school_class_id' => 'integer',

            'average_score' => 'decimal:2',

            'attendance_present' => 'integer',
            'attendance_sick' => 'integer',
            'attendance_permission' => 'integer',
            'attendance_absent' => 'integer',

            'is_finalized' => 'boolean',
            'finalized_at' => 'datetime',
        ];
    }

    /**
     * Siswa pemilik rapor.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Semester rapor.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Kelas siswa pada rapor tersebut.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Detail nilai mata pelajaran pada rapor.
     */
    public function details(): HasMany
    {
        return $this->hasMany(ReportCardDetail::class);
    }

    /**
     * Filter rapor yang sudah difinalisasi.
     */
    public function scopeFinalized(Builder $query): Builder
    {
        return $query->where('is_finalized', true);
    }

    /**
     * Filter rapor yang masih berupa draft.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('is_finalized', false);
    }

    /**
     * Mengunci atau memfinalisasi rapor.
     */
    public function finalize(): bool
    {
        return $this->update([
            'is_finalized' => true,
            'finalized_at' => now(),
        ]);
    }

    /**
     * Membuka kembali rapor.
     */
    public function reopen(): bool
    {
        return $this->update([
            'is_finalized' => false,
            'finalized_at' => null,
        ]);
    }
}
