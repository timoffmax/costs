<?php
declare(strict_types=1);

namespace App\Models\Statistic\Costs;

use App\Models\Statistic\StatisticAbstract;
use App\Transaction;

/**
 * Calculates costs by day
 */
class ByDay extends StatisticAbstract
{
    /**
     * @inheritdoc
     */
    public function getInfo(string $dateFrom, string $dateTo): array
    {
        $result = [];
        $transactionsByCurrency = $this->getCostsTransactions($dateFrom, $dateTo);

        foreach ($transactionsByCurrency as $currency => $transactions) {
            /** @var Transaction $transaction */
            foreach ($transactions as $transaction) {
                $date = (new \DateTime($transaction->date))->format('Y-m-d');

                $sum = $result[$date][$currency]['sum'] ?? 0;
                $sum += $transaction->sum;

                $summary = [
                    'date' => $date,
                    'sum' => $this->roundSum($sum),
                ];

                $result[$date][$currency] = $summary;
            }
        }

        ksort($result);

        return $result;
    }
}
