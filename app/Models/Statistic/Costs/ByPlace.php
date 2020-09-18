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

        foreach ($transactionsByCurrency as $currency => $transactions) {
            /** @var Transaction $transaction */
            foreach ($transactions as $transaction) {
                $placeName = $transaction->place ? $transaction->place->name : 'No place';
                $placeId = $transaction->place ? $transaction->place->id : null;

                $sum = $result[$placeName]['sum'][$currency] ?? 0;
                $sum += $transaction->sum;

                $result[$placeName]['id'] = $placeId;
                $result[$placeName]['sum'][$currency] = $this->roundSum($sum);
            }
        }

        arsort($result);

        return $result;
    }
}
