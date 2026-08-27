<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array {
        return [
            'id' => $this->id,

            'name' => $this->name,

            'permission_ids' => $this->whenLoaded(
                'permissions',
                fn () => $this->permissions
                    ->pluck('id')
                    ->values()
            ),

            'permissions' =>
                PermissionResource::collection(
                    $this->whenLoaded('permissions')
                ),

            'users_count' => $this->whenCounted(
                'users'
            ),

            'permissions_count' => $this->whenCounted(
                'permissions'
            ),

            'created_at' => $this->created_at
                ?->toISOString(),

            'updated_at' => $this->updated_at
                ?->toISOString(),
        ];
    }
}