<?php
declare(strict_types=1);

namespace App\Providers;

use App\Models\Api\CurrConv\Response\ConvertResponseFactory;
use Illuminate\Support\ServiceProvider;

/**
 * Allows automatic DI for factory classes
 */
class FactoryProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * @var array
     */
    public $singletons = [
        ConvertResponseFactory::class => ConvertResponseFactory::class,
    ];

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return [
            ConvertResponseFactory::class
        ];
    }
}
