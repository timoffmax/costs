<?php
declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResources([
    'dashboard' => 'API\DashboardController',
    'statistic' => 'API\StatisticController',
    'user' => 'API\UserController',
    'userRole' => 'API\UserRoleController',
    'account' => 'API\AccountController',
    'accountType' => 'API\AccountTypeController',
    'place' => 'API\PlaceController',
    'transaction' => 'API\TransactionController',
    'transactionType' => 'API\TransactionTypeController',
    'transactionCategory' => 'API\TransactionCategoryController',
    'transactionCategoryType' => 'API\TransactionCategoryTypeController',
    'currency' => 'API\CurrencyController',
]);

Route::get('statistic/{from}/{to}', 'API\StatisticController@index');
