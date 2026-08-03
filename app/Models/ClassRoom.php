<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    /**
     * Kolom yang boleh diisi melalui create() dan update().
     */
    protected $fillable = [
        'code',
        'name',
        'capacity',
    ];

    /**
     * Mengubah tipe data atribut saat dibaca dari database.
     */
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }
}
