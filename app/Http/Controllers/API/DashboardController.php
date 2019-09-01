<?php

namespace App\Http\Controllers\API;

use App\Models\Chart;
use App\Models\DashboardInfo;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

/**
 * Class DashboardController
 */
class DashboardController extends BaseController
{
    /**
     * @param Request $request
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        // User can view dashboard info if he/she is able to view transactions
        $userId = $request['userId'] ?? Auth::user()->id;
        $userModel = User::findOrFail($userId);
        $this->authorize('viewOwn', [Transaction::class, $userModel]);

        /**
         * @var DashboardInfo $dashboard
         * @var Chart $charts
         */
        $dashboard = App::getFacadeApplication()->make(DashboardInfo::class);
        $charts = App::getFacadeApplication()->make(Chart::class);

        $transactionsInfo = $dashboard->getTransactionsInfo();
        $chartsInfo = $charts->getInfo();

        $summaryInfo = [
            'transactions' => $transactionsInfo,
            'charts' => $chartsInfo,
        ];

        return $summaryInfo;
    }
}
