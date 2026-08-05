<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /**
     * Kolom yang boleh diisi melalui create() dan update().
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Daftar user yang memiliki role ini.
     *
     * Relasi ini digunakan jika tabel users memiliki kolom role_id.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Daftar permission yang dimiliki role ini.
     *
     * Menggunakan tabel pivot role_permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role_id',
            'permission_id'
        )->withTimestamps();
    }
}
