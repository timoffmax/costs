<?php
declare(strict_types=1);

namespace App\Models\Statistic;

/**
 * Class StatisticInterface
 */
interface StatisticInterface
{
    /**
     * Returns result
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getInfo(string $dateFrom, string $dateTo): array;
}
