<?php

namespace App\Providers;

use App\Account;
use App\AccountType;
use App\Place;
use App\Policies\AccountPolicy;
use App\Policies\AccountTypePolicy;
use App\Policies\PlacePolicy;
use App\Policies\TransactionCategoryPolicy;
use App\Policies\TransactionCategoryTypePolicy;
use App\Policies\TransactionPolicy;
use App\Policies\TransactionTypePolicy;
use App\Policies\UserPolicy;
use App\Policies\UserRolePolicy;
use App\Transaction;
use App\TransactionCategory;
use App\TransactionCategoryType;
use App\TransactionType;
use App\User;
use App\UserRole;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        User::class => UserPolicy::class,
        UserRole::class => UserRolePolicy::class,
        Account::class => AccountPolicy::class,
        Place::class => PlacePolicy::class,
        AccountType::class => AccountTypePolicy::class,
        Transaction::class => TransactionPolicy::class,
        TransactionType::class => TransactionTypePolicy::class,
        TransactionCategory::class => TransactionCategoryPolicy::class,
        TransactionCategoryType::class => TransactionCategoryTypePolicy::class,
    ];


    /**
     * Register any authentication / authorization services.
     *
     * @param Gate $gate
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
