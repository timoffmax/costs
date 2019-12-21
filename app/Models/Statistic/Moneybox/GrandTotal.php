<?php
declare(strict_types=1);

namespace App\Models\Statistic\Moneybox;

use App\Models\Statistic\TotalsAbstract;
use App\Transaction;

/**
 * Class GrandTotal
 */
class GrandTotal extends TotalsAbstract
{
    /**
     * Returns totals sum of money put to moneyboxes for the period
     *
     * @inheritDoc
     * @throws \Exception
     */
    public function getTotals(string $dateFrom, string $dateTo): float
    {
        $result = 0.0;

        $incomes = $this->getMoneyboxTransactions($dateFrom, $dateTo);

        /** @var Transaction $income */
        foreach ($incomes as $income) {
            $result += $income->sum;
        }

        $result = $this->roundSum($result);

        return $result;
    }
}
