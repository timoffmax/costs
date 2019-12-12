<?php
declare(strict_types=1);

namespace App\Models\Traits\User;

use App\User;
use Illuminate\Support\Facades\Auth;

/**
 * Trait CurrentUserTrait
 */
trait CurrentUserTrait
{
    /**
     * Returns current logged in user
     *
     * @return User
     */
    protected function getCurrentUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
