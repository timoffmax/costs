<?php
declare(strict_types=1);

namespace App\Models\Statistic;

use App\Models\Service\Transaction\GetByPeriod;
use App\TransactionType;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Class StatisticAbstract
 */
abstract class StatisticAbstract implements StatisticInterface
{
    /**
     * @var GetByPeriod
     */
    protected $getByPeriod;

    /**
     * Chart constructor.
     * @param GetByPeriod $getByPeriod
     */
    public function __construct(
        GetByPeriod $getByPeriod
    ) {
        $this->getByPeriod = $getByPeriod;
    }

    /**
     * Returns transactions for the specified period
     *
     * @return Collection
     * @throws \Exception
     */
    protected function getTransactions(string $from, string $to): Collection
    {
        $startDate = new \DateTime($from);
        $endDate = new \DateTime($to);

        /** @var User $user */
        $user = Auth::user();
        $transactions = $this->getByPeriod->execute($startDate, $endDate, $user);

        return $transactions;
    }

    /**
     * Returns only costs transactions
     *
     * @param string $from
     * @param string $to
     * @return Collection
     * @throws \Exception
     */
    protected function getCostsTransactions(string $from, string $to): Collection
    {
        $transactions = $this->getTransactions($from, $to);

        foreach ($transactions as $key => $transaction) {
            $notCost = $transaction->type->name !== TransactionType::TYPE_COST;
            $notUah = $transaction->account->currency;
            $dontCalculateCosts = !$transaction->account->calculate_costs;

            if ($notCost || $notUah || $dontCalculateCosts) {
                unset($transactions[$key]);
            }
        }

        return $transactions;
    }
}
