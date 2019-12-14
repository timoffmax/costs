<?php
declare(strict_types=1);

namespace App\Models\Statistic;

/**
 * Class StatisticInterface
 */
interface StatisticInterface
{
    /**
     * Returns total of calculations for the selected period
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getInfo(string $dateFrom, string $dateTo): array;
}
