<?php

namespace App\Providers;

use App\Account;
use App\AccountType;
use App\Policies\AccountPolicy;
use App\Policies\AccountTypePolicy;
use App\Policies\UserPolicy;
use App\Policies\UserRolePolicy;
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
        AccountType::class => AccountTypePolicy::class,
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
