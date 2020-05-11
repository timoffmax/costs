<?php
declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Controllers\API\UserController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User model resource for @see UserController
 */
class UserResource extends JsonResource
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
        /** @var User $this */
        return [
            'id' => $this->id,
            'role_id' => $this->role_id,
            'name' => $this->name,
            'email' => $this->email,
            'photo' => $this->photo,
            'role' => $this->role,
            'accounts' => $this->accounts,
            'places' => $this->places,
            'transactions_count' => $this->transactions->count(),
        ];
    }
}
