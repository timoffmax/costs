<?php
declare(strict_types=1);

namespace App\Models\Statistic\Costs;

use App\Models\Statistic\StatisticAbstract;
use App\Place;
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

        $transactions = $this->getTransactions($dateFrom, $dateTo);

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            /** @var Place $place */
            $placeName = $transaction->place ? $transaction->place->name : 'No place';

            $result[$placeName] = $result[$placeName] ?? 0;
            $result[$placeName] += $transaction->sum;
        }

        return $result;
    }
}
