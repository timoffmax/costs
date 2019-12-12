<?php
declare(strict_types = 1);

namespace App\Models;

use App\Models\Statistic\Costs\ByPlace as CostsByPlace;
use App\Models\Statistic\Costs\ByCategory as CostsByCategory;
use App\Models\Statistic\Costs\ByDay as CostsByDay;
use App\Models\Statistic\Costs\GrandTotal;

/**
 * Class Statistic
 */
class Statistic
{
    /**
     * @var CostsByPlace
     */
    private $costsByPlace;

    /**
     * @var CostsByCategory
     */
    private $costsByCategory;

    /**
     * @var CostsByDay
     */
    private $costsByDay;

    /**
     * @var GrandTotal
     */
    private $grandTotal;

    /**
     * Statistic constructor.
     * @param CostsByPlace $costsByPlace
     * @param CostsByCategory $costsByCategory
     * @param CostsByDay $costsByDay
     * @param GrandTotal $grandTotal
     */
    public function __construct(
        CostsByPlace $costsByPlace,
        CostsByCategory $costsByCategory,
        CostsByDay $costsByDay,
        GrandTotal $grandTotal
    ) {
        $this->costsByPlace = $costsByPlace;
        $this->costsByCategory = $costsByCategory;
        $this->costsByDay = $costsByDay;
        $this->grandTotal = $grandTotal;
    }

    /**
     * Returns prepared data
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     * @throws \Exception
     */
    public function getInfo(string $dateFrom, string $dateTo): array
    {
        $result = [
            'costs' => $this->getCostsInfo($dateFrom, $dateTo),
        ];

        return $result;
    }

    /**
     * Returns various statistic of costs
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     * @throws \Exception
     */
    public function getCostsInfo(string $dateFrom, string $dateTo): array
    {
        return [
            'byPlace' => $this->costsByPlace->getInfo($dateFrom, $dateTo),
            'byCategory' => $this->costsByCategory->getInfo($dateFrom, $dateTo),
            'byDay' => $this->costsByDay->getInfo($dateFrom, $dateTo),
            'grandTotal' => $this->grandTotal->getTotals($dateFrom, $dateTo),
        ];
    }
}
