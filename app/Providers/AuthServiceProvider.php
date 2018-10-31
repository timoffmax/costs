<?php

namespace App\Providers;

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

        Gate::define('isAdmin', function(User $user) {
            return $user->role->name === 'admin';
        });

        Passport::routes();
    }
}
