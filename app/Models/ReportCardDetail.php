<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCardDetail extends Model
{
    /**
     * Kolom yang boleh diisi melalui create() dan update().
     */
    protected $fillable = [
        'report_card_id',
        'teaching_assignment_id',
        'final_score',
        'predicate',
        'description',
    ];

    /**
     * Konversi tipe data atribut.
     */
    protected function casts(): array
    {
        return [
            'report_card_id' => 'integer',
            'teaching_assignment_id' => 'integer',
            'final_score' => 'decimal:2',
        ];
    }

    /**
     * Rapor utama yang memiliki detail ini.
     */
    public function reportCard(): BelongsTo
    {
        return $this->belongsTo(ReportCard::class);
    }

    /**
     * Penugasan mengajar atau mata pelajaran
     * yang berkaitan dengan detail nilai ini.
     */
    public function teachingAssignment(): BelongsTo
    {
        return $this->belongsTo(TeachingAssignment::class);
    }
}
