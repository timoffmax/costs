<?php
declare(strict_types=1);

namespace App\Policies;

use App\TransactionCategory;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

/**
 * Class TransactionCategoryPolicy
 */
class TransactionCategoryPolicy
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
     * Determine whether the user can view the models list.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAll(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param TransactionCategory $model
     * @return mixed
     */
    public function view(User $user, TransactionCategory $model)
    {
        return false;
    }

    /**
     * Used to automatically check whether the user can view index controller method
     *
     * @param User $user
     * @param TransactionCategory $model
     * @return bool
     */
    public function viewAny(User $user, TransactionCategory $model)
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param TransactionCategory $model
     * @return mixed
     */
    public function create(TransactionCategory $model)
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param TransactionCategory $model
     * @return mixed
     */
    public function update(User $user, TransactionCategory $model)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param TransactionCategory $model
     * @return mixed
     */
    public function delete(User $user, TransactionCategory $model)
    {
        return false;
    }
}
