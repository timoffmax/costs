<?php

namespace App\Providers;

use App\Models\Chart;
use App\Models\DashboardInfo;
use App\Models\Service\Transaction\GetByPeriod;
use Illuminate\Support\ServiceProvider;

/**
 * Class TransactionServiceProvider
 */
class TransactionServiceProvider extends ServiceProvider
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
        DashboardInfo::class => DashboardInfo::class,
        Chart::class => Chart::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
//        $this->app->singleton(GetByPeriod::class, function () {
//            return new GetByPeriod();
//        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            GetByPeriod::class,
            DashboardInfo::class,
            Chart::class,
        ];
    }
}
