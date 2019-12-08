<?php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Models\Chart;
use App\Models\DashboardInfo;
use App\Models\Statistic\Costs\ByPlace;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
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
    public function index(Request $request, ByPlace $byPlace, DashboardInfo $dashboard, Chart $charts)
    {
        // User can view dashboard info if he/she is able to view transactions
        $userId = $request['userId'] ?? Auth::user()->id;
        $userModel = User::findOrFail($userId);
        $this->authorize('viewOwn', [Transaction::class, $userModel]);

        $transactionsInfo = $dashboard->getTransactionsInfo();
        $chartsInfo = $charts->getInfo();

        $summaryInfo = [
            'transactions' => $transactionsInfo,
            'charts' => $chartsInfo,
        ];

        return $summaryInfo;
    }
}
