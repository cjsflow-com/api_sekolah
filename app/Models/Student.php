<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Status siswa
    |--------------------------------------------------------------------------
    */

    public const STATUS_ACTIVE = 'active';
    public const STATUS_GRADUATED = 'graduated';
    public const STATUS_MOVED = 'moved';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * Kolom yang boleh diisi melalui create(), update(),
     * firstOrCreate(), dan updateOrCreate().
     */
    protected $fillable = [
        'name',
        'nis',
        'nisn',
        'email',
        'phone',
        'password',
        'gender',
        'birth_place',
        'birth_date',
        'address',
        'parent_name',
        'parent_phone',
        'status',
        'avatar',
    ];

    /**
     * Kolom yang tidak ditampilkan ketika model
     * dikonversi menjadi array atau JSON.
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
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Mengambil siswa yang masih aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Search student.
     */
    public function scopeSearch(
        Builder $query,
        ?string $search
    ): Builder {
        return $query->when(
            filled($search),
            function (Builder $query) use ($search): void {
                $query->where(
                    function (Builder $query) use ($search): void {
                        $query
                            ->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'nis',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'nisn',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'email',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'phone',
                                'like',
                                "%{$search}%"
                            );
                    }
                );
            }
        );
    }

    /**
     * Riwayat absensi siswa.
     */
    public function classAttendances(): HasMany
    {
        return $this->hasMany(ClassAttendance::class);
    }

    /**
     * Daftar rapor siswa.
     */
    public function reportCards(): HasMany
    {
        return $this->hasMany(ReportCard::class);
    }

    /**
     * Daftar tagihan siswa.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
