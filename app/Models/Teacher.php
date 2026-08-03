<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Teacher extends Authenticatable
{
    use Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Konstanta jenis identitas
    |--------------------------------------------------------------------------
    */

    public const IDENTITY_TYPE_NIP = 'NIP';
    public const IDENTITY_TYPE_NUPTK = 'NUPTK';

    /*
    |--------------------------------------------------------------------------
    | Konstanta gender
    |--------------------------------------------------------------------------
    |
    | Sesuaikan aturan ini dengan sistem yang kamu buat.
    |
    */

    public const GENDER_MALE = 1;
    public const GENDER_FEMALE = 2;

    /**
     * Kolom yang boleh diisi melalui create() dan update().
     */
    protected $fillable = [
        'identity_type',
        'identity_number',
        'name',
        'email',
        'phone',
        'password',
        'gender',
        'birth_place',
        'birth_date',
        'address',
        'avatar',
        'is_active',
    ];

    /**
     * Kolom yang tidak ditampilkan dalam array atau JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Konversi tipe data atribut.
     */
    protected function casts(): array
    {
        return [
            'gender' => 'integer',
            'birth_date' => 'date',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Mengambil guru yang statusnya aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Penugasan mengajar milik guru.
     */
    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class);
    }

    /**
     * Data kehadiran kelas yang dicatat guru.
     */
    public function recordedClassAttendances(): HasMany
    {
        return $this->hasMany(
            ClassAttendance::class,
            'recorded_by'
        );
    }
}
