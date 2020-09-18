<?php
declare(strict_types=1);

namespace App\Models\Statistic\Costs;

use App\Models\Statistic\StatisticAbstract;
use App\Transaction;

/**
 * Calculates costs by category
 */
class ByCategory extends StatisticAbstract
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
                $categoryId = $transaction->category->id;
                $categoryName = $transaction->category->name;

                $sum = $result[$categoryId]['sum'][$currency] ?? 0;
                $sum += $transaction->sum;

                $result[$categoryId]['name'] = $categoryName;
                $result[$categoryId]['sum'][$currency] = $this->roundSum($sum);
            }

            arsort($result[$categoryId]['sum']);
        }

        return $result;
    }
}
