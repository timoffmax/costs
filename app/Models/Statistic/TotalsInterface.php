<?php
declare(strict_types=1);

namespace App\Models\Statistic;

/**
 * Class TotalsInterface
 */
interface TotalsInterface
{
    /**
     * Returns total of calculations for the selected period
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return float
     */
    public function getTotals(string $dateFrom, string $dateTo): float;
}
