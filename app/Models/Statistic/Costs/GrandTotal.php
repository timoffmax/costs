<?php
declare(strict_types=1);

namespace App\Models\Statistic\Costs;

use App\Models\Statistic\TotalsAbstract;
use App\Transaction;

/**
 * Class Total
 */
class GrandTotal extends TotalsAbstract
{
    /**
     * Returns totals sum of costs for the period
     *
     * @inheritDoc
     * @throws \Exception
     */
    public function getTotals(string $dateFrom, string $dateTo): float
    {
        $result = 0.0;

        $costs = $this->getCostsTransactions($dateFrom, $dateTo);

        /** @var Transaction $cost */
        foreach ($costs as $cost) {
            $result += $cost->sum;
        }

        $result = $this->roundSum($result);

        return $result;
    }
}
