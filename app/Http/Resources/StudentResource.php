<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StudentResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array {
        return [
            'id' => $this->id,

            'name' => $this->name,

            'nis' => $this->nis,

            'nisn' => $this->nisn,

            'email' => $this->email,

            'phone' => $this->phone,

            'gender' => $this->gender,

            'gender_label' => match ($this->gender) {
                1 => 'Laki-laki',
                2 => 'Perempuan',
                default => null,
            },

            'birth_place' => $this->birth_place,

            'birth_date' => $this->birth_date
                ?->format('Y-m-d'),

            'address' => $this->address,

            'parent' => [
                'name' => $this->parent_name,
                'phone' => $this->parent_phone,
            ],

            'status' => $this->status,

            'status_label' => match ($this->status) {
                'active' => 'Aktif',
                'graduated' => 'Lulus',
                'moved' => 'Pindah',
                'inactive' => 'Tidak Aktif',
                default => null,
            },

            'avatar' => $this->avatar,

            'avatar_url' => $this->avatar
                ? Storage::disk('public')
                    ->url($this->avatar)
                : null,

            'last_login_at' => $this->last_login_at
                ?->toISOString(),

            'created_at' => $this->created_at
                ?->toISOString(),

            'updated_at' => $this->updated_at
                ?->toISOString(),
        ];
    }
}
