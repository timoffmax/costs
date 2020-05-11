<?php
declare(strict_types=1);

namespace App\Policies;

use App\User;
use App\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Auth policies for UserRole model
 */
class UserRolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param UserRole $model
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(User $user, UserRole $model): bool
    {
        return false;
    }

    /**
     * Used to automatically check whether the user can view index controller method
     *
     * @param User $user
     * @param UserRole $model
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function viewAny(User $user, UserRole $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param UserRole $model
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function update(User $user, UserRole $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param UserRole $model
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function delete(User $user, UserRole $model): bool
    {
        return false;
    }
}
