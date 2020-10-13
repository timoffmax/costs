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
        $currencyCourses = $this->getCurrencyCourses();

        foreach ($transactionsByCurrency as $currency => $transactions) {
            $course = $currencyCourses[$currency] ?? 1;

            /** @var Transaction $transaction */
            foreach ($transactions as $transaction) {
                $date = (new \DateTime($transaction->date))->format('Y-m-d');

                $sum = $result[$date][$currency]['sum'] ?? 0;
                $baseCurrencySum = $result[$date]['base']['sum'] ?? 0;

                $sum += $transaction->sum;
                $baseCurrencySum += ($transaction->sum * $course);

                $summary = [
                    'date' => $date,
                    'sum' => $this->roundSum($sum),
                ];

                $baseSummary = [
                    'date' => $date,
                    'sum' => $this->roundSum($baseCurrencySum),
                ];

                $result[$date][$currency] = $summary;
                $result[$date]['base'] = $baseSummary;
            }
        }

        ksort($result);

        return $result;
    }
}
