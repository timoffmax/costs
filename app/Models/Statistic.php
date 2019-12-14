<?php
declare(strict_types = 1);

namespace App\Models;

use App\Models\Statistic\Costs\ByAccount;
use App\Models\Statistic\Costs\ByPlace as CostsByPlace;
use App\Models\Statistic\Costs\ByCategory as CostsByCategory;
use App\Models\Statistic\Costs\ByDay as CostsByDay;
use App\Models\Statistic\Costs\GrandTotal as CostsGrandTotal;
use App\Models\Statistic\Incomes\GrandTotal as IncomesGrandTotal;

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
     * @var CostsGrandTotal
     */
    private $costsGrandTotal;

    /**
     * @var IncomesGrandTotal
     */
    private $incomesGrandTotal;

    /**
     * @var ByAccount
     */
    private $costsByAccount;

    /**
     * Statistic constructor.
     * @param CostsByPlace $costsByPlace
     * @param CostsByCategory $costsByCategory
     * @param CostsByDay $costsByDay
     * @param CostsGrandTotal $costsGrandTotal
     * @param ByAccount $costsByAccount
     * @param IncomesGrandTotal $incomesGrandTotal
     */
    public function __construct(
        CostsByPlace $costsByPlace,
        CostsByCategory $costsByCategory,
        CostsByDay $costsByDay,
        ByAccount $costsByAccount,
        CostsGrandTotal $costsGrandTotal,
        IncomesGrandTotal $incomesGrandTotal
    ) {
        $this->costsByPlace = $costsByPlace;
        $this->costsByCategory = $costsByCategory;
        $this->costsByDay = $costsByDay;
        $this->costsGrandTotal = $costsGrandTotal;
        $this->incomesGrandTotal = $incomesGrandTotal;
        $this->costsByAccount = $costsByAccount;
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
            'incomes' => $this->getIncomesInfo($dateFrom, $dateTo),
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
            'byAccount' => $this->costsByAccount->getInfo($dateFrom, $dateTo),
            'grandTotal' => $this->costsGrandTotal->getTotals($dateFrom, $dateTo),
        ];
    }

    /**
     * Returns various statistic of incomes
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     * @throws \Exception
     */
    public function getIncomesInfo(string $dateFrom, string $dateTo): array
    {
        return [
            'grandTotal' => $this->costsGrandTotal->getTotals($dateFrom, $dateTo),
        ];
    }
}
