<?php
declare(strict_types = 1);

namespace App\Models;

use App\Models\Service\Transaction\GetByPeriod;
use App\Transaction;
use App\User;

/**
 * Class Chart
 */
class Chart
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var GetByPeriod
     */
    private $getByPeriod;

    /**
     * Chart constructor.
     * @param GetByPeriod $getByPeriod
     * @param User $user
     */
    public function __construct(
        GetByPeriod $getByPeriod,
        User $user
    ) {
        $this->user = $user;
        $this->getByPeriod = $getByPeriod;
    }

    /**
     * Returns charts prepared data
     *
     * @return array
     */
    public function getInfo(): array
    {
        $chartsInfo = [
            'thisMonth' => $this->getThisMonthInfo(),
            'lastMonth' => $this->getLastMonthInfo(),
        ];

        return $chartsInfo;
    }

    /**
     * Returns calculated total by transaction type
     *
     * @param array $transactions
     * @param string $type
     * @return float
     */
    protected function getTotalCostsByCategories(array $transactions): array
    {
        $costsByCategories = [];

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            if ('cost' === $transaction->type->name) {
                $categoryName = $transaction->category->name;

                $categoryCosts = $costsByCategories[$categoryName] ?? 0;
                $categoryCosts += $transaction->sum;

                $costsByCategories[$categoryName] = $categoryCosts;
            }
        }

        arsort($costsByCategories);

        return $costsByCategories;
    }

    /**
     * Prepares info for the current month
     *
     * @return array
     */
    protected function getThisMonthInfo(): array
    {
        $costsByCategory = $this->getTotalCostsByCategories($this->getThisMonthTransactions());

        return [
            'costs' => [
                'byCategory' => $costsByCategory,
            ],
        ];
    }

    /**
     * Prepares info for the last month
     *
     * @return array
     */
    protected function getLastMonthInfo(): array
    {
        $costsByCategory = $this->getTotalCostsByCategories($this->getLastMonthTransactions());

        return [
            'costs' => [
                'byCategory' => $costsByCategory,
            ],
        ];
    }

    /**
     * Returns this month transactions
     *
     * @return array
     */
    protected function getThisMonthTransactions(): array
    {
        $thisMonthStart = new \DateTime('first day of this month');
        $now = new \DateTime();

        $transactions = $this->getByPeriod->getByDates($thisMonthStart, $now, $this->user);

        return $transactions;
    }

    /**
     * Returns last month transactions
     *
     * @return array
     */
    protected function getLastMonthTransactions(): array
    {
        $lastMonthStart = new \DateTime('first day of last month');
        $lastMonthEnd = new \DateTime('last day of last month');

        $transactions = $this->getByPeriod->getByDates($lastMonthStart, $lastMonthEnd, $this->user);

        return $transactions;
    }
}
