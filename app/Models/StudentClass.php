<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentClass extends Model
{
    /**
     * Kolom yang boleh diisi menggunakan create() dan update().
     */
    protected $fillable = [
        'student_id',
        'school_class_id',
        'academic_year_id',
    ];

    /**
     * Konversi tipe data atribut.
     */
    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'school_class_id' => 'integer',
            'academic_year_id' => 'integer',
        ];
    }

    /**
     * Siswa yang ditempatkan ke kelas.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Kelas tempat siswa ditempatkan.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Tahun ajaran penempatan siswa.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
