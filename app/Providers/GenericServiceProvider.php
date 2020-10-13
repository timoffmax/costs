<?php
declare(strict_types=1);

namespace App\Providers;

use App\Models\Service\Api\CurrConv\Request\Convert as CurrConvConvert;
use App\Models\Service\Api\CurrConv\SendRequest as CurrConvSendRequest;
use App\Models\Service\Currency\GetCourses as GetCurrencyCourses;
use Illuminate\Support\ServiceProvider;

/**
 * Allows automatic DI for classes that classified as services
 */
class GenericServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * @var array
     */
    public $singletons = [
        CurrConvSendRequest::class => CurrConvSendRequest::class,
        CurrConvConvert::class => CurrConvConvert::class,
        GetCurrencyCourses::class => GetCurrencyCourses::class,
    ];

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return [
            CurrConvSendRequest::class,
            CurrConvConvert::class,
        ];
    }
}
