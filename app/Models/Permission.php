<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    /**
     * Kolom yang boleh diisi melalui create() dan update().
     */
    protected $fillable = [
        'module',
        'name',
        'action',
    ];

    /**
     * Role yang memiliki permission ini.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_permissions',
            'permission_id',
            'role_id'
        )->withTimestamps();
    }

    /**
     * Filter permission berdasarkan modul.
     *
     * Contoh:
     * Permission::module('students')->get();
     */
    public function scopeModule(
        Builder $query,
        string $module
    ): Builder {
        return $query->where('module', $module);
    }

    /**
     * Filter permission berdasarkan action.
     *
     * Contoh:
     * Permission::action('create')->get();
     */
    public function scopeAction(
        Builder $query,
        string $action
    ): Builder {
        return $query->where('action', $action);
    }

    /**
     * Membuat kode permission.
     *
     * Contoh hasil:
     * students.view
     * students.create
     */
    public function getCodeAttribute(): string
    {
        return "{$this->module}.{$this->action}";
    }
}
