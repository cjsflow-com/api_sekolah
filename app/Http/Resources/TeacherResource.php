<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TeacherResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array {
        return [
            'id' => $this->id,

            'identity' => [
                'type' => $this->identity_type,
                'number' => $this->identity_number,
            ],

            'name' => $this->name,

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

            'avatar' => $this->avatar,

            'avatar_url' => $this->avatar
                ? Storage::disk('public')
                    ->url($this->avatar)
                : null,

            'is_active' => $this->is_active,

            'last_login_at' => $this->last_login_at
                ?->toISOString(),

            'created_at' => $this->created_at
                ?->toISOString(),

            'updated_at' => $this->updated_at
                ?->toISOString(),
        ];
    }
}
