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
            $accountName = $transaction->account->name;

            $result[$accountName] = $result[$accountName] ?? 0;
            $result[$accountName] += $transaction->sum;
            $result[$accountName] = $this->roundSum($result[$accountName]);
        }

        ksort($result);

        return $result;
    }
}
