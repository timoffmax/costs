<?php

namespace App\Http\Controllers\API;

use App\Models\DashboardInfo;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class DashboardController
 */
class DashboardController extends BaseController
{

    public function index(Request $request)
    {
        // User can view dashboard info if he/she is able to view transactions
        $userId = $request['userId'] ?? Auth::user()->id;
        $userModel = User::findOrFail($userId);
        $this->authorize('viewOwn', [Transaction::class, $userModel]);

        $dashboard = new DashboardInfo();
        $transactionsInfo = $dashboard->getTransactionsInfo();

        $summaryInfo = [
            'transactions' => $transactionsInfo,
        ];

        return $summaryInfo;
    }
}
