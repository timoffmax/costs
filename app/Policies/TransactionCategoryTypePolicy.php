<?php

namespace App\Policies;

use App\TransactionCategoryType;
use App\User;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

/**
 * Class TransactionCategoryTypePolicy
 */
class TransactionCategoryTypePolicy
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
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAll(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\TransactionCategoryType $model
     * @return mixed
     */
    public function view(User $user, TransactionCategoryType $model)
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\TransactionCategoryType $model
     * @return mixed
     */
    public function create(TransactionCategoryType $model)
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\TransactionCategoryType  $model
     * @return mixed
     */
    public function update(User $user, TransactionCategoryType $model)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\TransactionCategoryType  $model
     * @return mixed
     */
    public function delete(User $user, TransactionCategoryType $model)
    {
        return false;
    }
}
