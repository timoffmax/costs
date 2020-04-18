<?php
declare(strict_types=1);

namespace App\Models\Statistic\Costs;

use App\Models\Statistic\StatisticAbstract;
use App\Transaction;

/**
 * Class ByDay
 */
class ByDay extends StatisticAbstract
{
    /**
     * Returns result
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
            $date = (new \DateTime($transaction->date))->format('Y-m-d');

            $sum = $result[$date]['sum'] ?? 0;
            $sum += $transaction->sum;

            $result[$date]['sum'] = $this->roundSum($sum);
            $result[$date]['date'] = $date;
        }

        ksort($result);

        return $result;
    }
}
