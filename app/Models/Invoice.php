<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Status invoice
    |--------------------------------------------------------------------------
    */

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PAID = 'paid';
    public const STATUS_PARTIAL = 'partial';

    /**
     * Kolom yang boleh diisi menggunakan create() dan update().
     */
    protected $fillable = [
        'invoice_number',
        'student_id',
        'fee_type_id',
        'semester_id',
        'month',
        'year',
        'total_amount',
        'amount',
        'status',
        'due_date',
        'description',
    ];

    /**
     * Konversi tipe data atribut.
     */
    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'fee_type_id' => 'integer',
            'semester_id' => 'integer',
            'month' => 'integer',
            'year' => 'integer',
            'total_amount' => 'decimal:2',
            'amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    /**
     * Siswa pemilik invoice.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Jenis biaya invoice.
     */
    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class);
    }

    /**
     * Semester invoice.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Filter invoice yang belum dibayar.
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_UNPAID);
    }

    /**
     * Filter invoice yang dibayar sebagian.
     */
    public function scopePartial(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PARTIAL);
    }

    /**
     * Filter invoice yang sudah lunas.
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Filter invoice berdasarkan siswa.
     */
    public function scopeForStudent(
        Builder $query,
        int $studentId
    ): Builder {
        return $query->where('student_id', $studentId);
    }

    /**
     * Menghitung sisa tagihan.
     */
    public function getRemainingAmountAttribute(): string
    {
        $remaining = max(
            0,
            (float) $this->total_amount - (float) $this->amount
        );

        return number_format($remaining, 2, '.', '');
    }
}
