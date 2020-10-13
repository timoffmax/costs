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
        $currencyCourses = $this->getCurrencyCourses();

        foreach ($transactionsByCurrency as $currency => $transactions) {
            $course = $currencyCourses[$currency] ?? 1;

            /** @var Transaction $transaction */
            foreach ($transactions as $transaction) {
                $categoryId = $transaction->category->id;
                $categoryName = $transaction->category->name;

                $sum = $result[$categoryId]['sum'][$currency] ?? 0;
                $baseCurrencySum = $result[$categoryId]['sum']['base'] ?? 0;

                $sum += $transaction->sum;
                $baseCurrencySum += ($transaction->sum * $course);

                $result[$categoryId]['name'] = $categoryName;
                $result[$categoryId]['sum'][$currency] = $this->roundSum($sum);
                $result[$categoryId]['sum']['base'] = $this->roundSum($baseCurrencySum);
            }
        }

        $this->sortByBaseSum($result);

        return $result;
    }

    /**
     * @param array $data
     */
    private function sortByBaseSum(array &$data): void
    {
        uasort($data, function (array $elem1, array $elem2) {
            $sumA = $elem1['sum']['base'] ?? 0;
            $sumB = $elem2['sum']['base'] ?? 0;
            $result = $sumA - $sumB;

            return $result;
        });

        $data = array_reverse($data);
    }
}
