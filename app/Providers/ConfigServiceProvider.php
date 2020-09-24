<?php
declare(strict_types=1);

namespace App\Providers;

use App\Models\Api\CurrConv\Config;
use Illuminate\Support\ServiceProvider;

/**
 * Allows automatic DI for config classes
 */
class ConfigServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * @var array
     */
    public $singletons = [
        Config::class => Config::class,
    ];

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return [
            Config::class
        ];
    }
}
