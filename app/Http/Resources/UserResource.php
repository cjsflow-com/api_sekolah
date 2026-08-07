<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;


class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,

            'email' => $this->email,

            'avatar' => $this->avatar,

            'avatar_url' => $this->avatar
                ? Storage::disk('public')->url($this->avatar)
                : null,

            'is_active' => $this->is_active,

            'role' => $this->whenLoaded(
                'role',
                function (): ?array {
                    if (! $this->role) {
                        return null;
                    }

                    return [
                        'id' => $this->role->id,
                        'name' => $this->role->name,

                        'permissions' => $this->role
                            ->permissions
                            ->map(function ($permission): array {
                                return [
                                    'id' => $permission->id,
                                    'module' => $permission->module,
                                    'name' => $permission->name,
                                    'action' => $permission->action,

                                    'code' => sprintf(
                                        '%s.%s',
                                        $permission->module,
                                        $permission->action
                                    ),
                                ];
                            })
                            ->values(),
                    ];
                }
            ),

            'email_verified_at' => $this->email_verified_at
                ?->toISOString(),

            'created_at' => $this->created_at
                ?->toISOString(),

            'updated_at' => $this->updated_at
                ?->toISOString(),
        ];
    }
}