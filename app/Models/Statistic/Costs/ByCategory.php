<?php
declare(strict_types=1);

namespace App\Models\Statistic\Costs;

use App\Models\Statistic\StatisticAbstract;
use App\Transaction;

/**
 * Class ByCategory
 */
class ByCategory extends StatisticAbstract
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
            $categoryId = $transaction->category->id;
            $categoryName = $transaction->category->name;

            $sum = $result[$categoryId]['sum'] ?? 0;
            $sum += $transaction->sum;

            $result[$categoryId]['id'] = $categoryId;
            $result[$categoryId]['sum'] = $this->roundSum($sum);
            $result[$categoryId]['name'] = $categoryName;
        }

        arsort($result);

        return $result;
    }
}
