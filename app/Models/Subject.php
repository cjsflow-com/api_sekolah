<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    /**
     * Kolom yang boleh diisi menggunakan create() atau update().
     */
    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    /**
     * Daftar penugasan mengajar untuk mata pelajaran ini.
     */
    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class);
    }
}
