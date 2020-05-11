<?php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Models\Statistic;
use App\Transaction;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Statistic controller
 */
class StatisticController extends BaseController
{
    /**
     * @param Request $request
     * @param Statistic $statistic
     * @return array
     * @throws AuthorizationException
     * @throws \Exception
     */
    public function index(Request $request, string $from, string $to, Statistic $statistic)
    {
        /** @var User $user */
        $user = Auth::user();
        $userId = $request['userId'] ?? $user->id;
        $userModel = User::findOrFail($userId);
        $this->authorize('viewOwn', [Transaction::class, $userModel]);

        $result = $statistic->getInfo($from, $to);

        return $result;
    }
}
