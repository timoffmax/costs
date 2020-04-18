<?php
declare(strict_types=1);

namespace App\Models\Statistic\Costs;

use App\Models\Statistic\StatisticAbstract;
use App\Transaction;

/**
 * Class ByAccount
 */
class ByAccount extends StatisticAbstract
{
    /**
     * Returns result of ca
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     * @throws \Exception
     */
    public function getInfo(string $dateFrom, string $dateTo): array
    {
        $result = [];

        $transactions = $this->getCostsTransactions($dateFrom, $dateTo);

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $accountId = $transaction->account->id;
            $accountName = $transaction->account->name;

            $sum = $result[$accountId]['sum'] ?? 0;
            $sum += $transaction->sum;

            $result[$accountId]['id'] = $accountId;
            $result[$accountId]['name'] = $accountName;
            $result[$accountId]['sum'] = $this->roundSum($sum);
        }

        ksort($result);

        return $result;
    }
}
