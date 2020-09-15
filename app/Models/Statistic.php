<?php
declare(strict_types = 1);

namespace App\Models;

use App\Models\Statistic\Costs\ByAccount;
use App\Models\Statistic\Costs\ByPlace as CostsByPlace;
use App\Models\Statistic\Costs\ByCategory as CostsByCategory;
use App\Models\Statistic\Costs\ByDay as CostsByDay;
use App\Models\Statistic\Costs\GrandTotal as CostsGrandTotal;
use App\Models\Statistic\Deposits\GrandTotal as DepositsGrandTotal;
use App\Models\Statistic\Incomes\GrandTotal as IncomesGrandTotal;
use App\Models\Statistic\Moneybox\GrandTotal as MoneyboxGrandTotal;
use App\Models\Statistic\Savings\GrandTotal as SavingsGrandTotal;

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
     * @var DepositsGrandTotal
     */
    private $depositsGrandTotal;

    /**
     * @var MoneyboxGrandTotal
     */
    private $moneyboxGrandTotal;

    /**
     * @var SavingsGrandTotal
     */
    private $savingsGrandTotal;

    /**
     * Statistic constructor.
     * @param CostsByPlace $costsByPlace
     * @param CostsByCategory $costsByCategory
     * @param CostsByDay $costsByDay
     * @param ByAccount $costsByAccount
     * @param CostsGrandTotal $costsGrandTotal
     * @param IncomesGrandTotal $incomesGrandTotal
     * @param DepositsGrandTotal $depositsGrandTotal
     * @param MoneyboxGrandTotal $moneyboxGrandTotal
     * @param SavingsGrandTotal $savingsGrandTotal
     */
    public function __construct(
        CostsByPlace $costsByPlace,
        CostsByCategory $costsByCategory,
        CostsByDay $costsByDay,
        ByAccount $costsByAccount,
        CostsGrandTotal $costsGrandTotal,
        IncomesGrandTotal $incomesGrandTotal,
        DepositsGrandTotal $depositsGrandTotal,
        MoneyboxGrandTotal $moneyboxGrandTotal,
        SavingsGrandTotal $savingsGrandTotal
    ) {
        $this->costsByPlace = $costsByPlace;
        $this->costsByCategory = $costsByCategory;
        $this->costsByDay = $costsByDay;
        $this->costsGrandTotal = $costsGrandTotal;
        $this->incomesGrandTotal = $incomesGrandTotal;
        $this->costsByAccount = $costsByAccount;
        $this->depositsGrandTotal = $depositsGrandTotal;
        $this->moneyboxGrandTotal = $moneyboxGrandTotal;
        $this->savingsGrandTotal = $savingsGrandTotal;
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
            'deposits' => $this->getDepositsInfo($dateFrom, $dateTo),
            'moneybox' => $this->getMoneyboxInfo($dateFrom, $dateTo),
            'savings' => $this->getSavingsInfo($dateFrom, $dateTo),
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
            'totalsByCurrency' => $this->costsGrandTotal->getTotalsByCurrency($dateFrom, $dateTo),
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
            'grandTotal' => $this->incomesGrandTotal->getTotals($dateFrom, $dateTo),
            'totalsByCurrency' => $this->incomesGrandTotal->getTotalsByCurrency($dateFrom, $dateTo),
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
    public function getDepositsInfo(string $dateFrom, string $dateTo): array
    {
        return [
            'grandTotal' => $this->depositsGrandTotal->getTotals($dateFrom, $dateTo),
            'totalsByCurrency' => $this->depositsGrandTotal->getTotalsByCurrency($dateFrom, $dateTo),
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
    public function getMoneyboxInfo(string $dateFrom, string $dateTo): array
    {
        return [
            'grandTotal' => $this->moneyboxGrandTotal->getTotals($dateFrom, $dateTo),
            'totalsByCurrency' => $this->moneyboxGrandTotal->getTotalsByCurrency($dateFrom, $dateTo),
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
    public function getSavingsInfo(string $dateFrom, string $dateTo): array
    {
        $result = [
            'grandTotal' => $this->savingsGrandTotal->getTotals($dateFrom, $dateTo),
            'totalsByCurrency' => $this->savingsGrandTotal->getTotalsByCurrency($dateFrom, $dateTo),
        ];

        return $result;
    }
}
