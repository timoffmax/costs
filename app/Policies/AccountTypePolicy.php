<?php

namespace App\Policies;

use App\AccountType;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class AccountTypePolicy
{
    use HandlesAuthorization;

    /**
     * Don't check permissions for admin
     *
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before($user, $ability): bool
    {
        if (Auth::user()->role->name === 'admin') {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAll(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\AccountType $model
     * @return mixed
     */
    public function view(User $user, AccountType $model)
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\AccountType $model
     * @return mixed
     */
    public function create(AccountType $account)
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\AccountType  $model
     * @return mixed
     */
    public function update(User $user, AccountType $model)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\AccountType $model
     * @return mixed
     */
    public function delete(User $user, AccountType $model)
    {
        return false;
    }
}
