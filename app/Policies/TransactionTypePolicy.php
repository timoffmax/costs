<?php
declare(strict_types=1);

namespace App\Policies;

use App\TransactionType;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class TransactionTypePolicy
{
    use HandlesAuthorization;

    /**
     * Don't check permissions for admin
     *
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before($user, $ability)
    {
        if (Auth::user()->role->name === 'admin') {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @return bool
     */
    public function viewAll(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param TransactionType $model
     * @return bool
     */
    public function view(User $user, TransactionType $model): bool
    {
        return false;
    }

    /**
     * Used to automatically check whether the user can view index controller method
     *
     * @param User $user
     * @param TransactionType $model
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function viewAny(User $user, TransactionType $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param TransactionType $model
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function create(TransactionType $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param TransactionType $model
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function update(User $user, TransactionType $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param TransactionType $model
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function delete(User $user, TransactionType $model): bool
    {
        return false;
    }
}
