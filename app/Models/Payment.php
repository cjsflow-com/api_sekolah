<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    public const METHOD_CASH = 'cash';
    public const METHOD_TRANSFER = 'transfer';
    public const METHOD_E_WALLET = 'e-wallet';

    protected $fillable = [
        'invoice_id',
        'reference_number',
        'payment_date',
        'amount',
        'payment_method',
        'reference_no',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'invoice_id' => 'integer',
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function receivedBy(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeCash(Builder $query): Builder
    {
        return $query->where(
            'payment_method',
            self::METHOD_CASH
        );
    }

    public function scopeTransfer(Builder $query): Builder
    {
        return $query->where(
            'payment_method',
            self::METHOD_TRANSFER
        );
    }

    public function scopeEWallet(Builder $query): Builder
    {
        return $query->where(
            'payment_method',
            self::METHOD_E_WALLET
        );
    }
}
