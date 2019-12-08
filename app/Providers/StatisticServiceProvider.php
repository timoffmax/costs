<?php

namespace App\Providers;

use App\Models\Service\Transaction\GetByPeriod;
use App\Models\Statistic\Costs\ByPlace as CostsByPlace;
use App\User;
use Illuminate\Support\ServiceProvider;

/**
 * Class StatisticServiceProvider
 */
class StatisticServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * @var array
     */
    public $singletons = [
        GetByPeriod::class => GetByPeriod::class,
        CostsByPlace::class => CostsByPlace::class,
        User::class => User::class,
    ];


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            GetByPeriod::class,
            CostsByPlace::class,
            User::class,
        ];
    }
}
