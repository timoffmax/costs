<?php
declare(strict_types=1);

namespace App\Http\Resources;

use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User role model resource for @see UserRoleController
 */
class UserRoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($request): array
    {
        /** @var UserRole $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
