<?php
declare(strict_types=1);

namespace App\Models\Statistic\Costs;

use App\Models\Statistic\StatisticAbstract;
use App\Transaction;

/**
 * Calculates costs by place
 */
class ByPlace extends StatisticAbstract
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
                $placeName = $transaction->place ? $transaction->place->name : 'No place';
                $placeId = $transaction->place ? $transaction->place->id : null;

                $sum = $result[$placeName]['sum'][$currency] ?? 0;
                $baseCurrencySum = $result[$placeName]['sum']['base'] ?? 0;

                $sum += $transaction->sum;
                $baseCurrencySum += ($transaction->sum * $course);

                $result[$placeName]['id'] = $placeId;
                $result[$placeName]['sum'][$currency] = $this->roundSum($sum);
                $result[$placeName]['sum']['base'] = $this->roundSum($baseCurrencySum);
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
