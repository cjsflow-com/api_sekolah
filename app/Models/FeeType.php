<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeType extends Model
{
    /**
     * Kolom yang boleh diisi melalui create() dan update().
     */
    protected $fillable = [
        'name',
        'is_active',
    ];

    /**
     * Konversi tipe data atribut model.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Mengambil jenis biaya yang masih aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Daftar invoice yang menggunakan jenis biaya ini.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
