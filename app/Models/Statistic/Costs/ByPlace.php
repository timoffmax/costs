<?php
declare(strict_types=1);

namespace App\Models\Statistic\Costs;

use App\Models\Statistic\StatisticAbstract;
use App\Transaction;

/**
 * Class ByPlace
 */
class ByPlace extends StatisticAbstract
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
            $placeName = $transaction->place ? $transaction->place->name : 'No place';
            $placeId = $transaction->place ? $transaction->place->id : null;

            $sum = $result[$placeName]['sum'] ?? 0;
            $sum += $transaction->sum;

            $result[$placeName]['id'] = $placeId;
            $result[$placeName]['name'] = $placeName;
            $result[$placeName]['sum'] = $this->roundSum($sum);
        }

        arsort($result);

        return $result;
    }
}
