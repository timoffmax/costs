<?php
declare(strict_types=1);

namespace App\Models\Statistic;

use App\Models\Service\Transaction\GetByPeriod;
use App\Models\Traits\User\CurrentUserTrait;
use App\TransactionType;

/**
 * Class StatisticAbstract
 */
abstract class StatisticAbstract implements StatisticInterface
{
    use CurrentUserTrait;

    /**
     * @var GetByPeriod
     */
    protected $getByPeriod;

    /**
     * StatisticAbstract constructor.
     * @param GetByPeriod $getByPeriod
     */
    public function __construct(GetByPeriod $getByPeriod)
    {
        $this->getByPeriod = $getByPeriod;
    }

    /**
     * Returns transactions for the specified period
     *
     * @return array
     * @throws \Exception
     */
    protected function getTransactions(string $from, string $to): array
    {
        $transactions = $this->getByPeriod->getByStringDates($from, $to, $this->getCurrentUser());

        return $transactions;
    }

    /**
     * Returns only costs transactions
     *
     * @param string $from
     * @param string $to
     * @return array
     * @throws \Exception
     */
    protected function getCostsTransactions(string $from, string $to): array
    {
        $transactions = $this->getByPeriod->getByStringDates(
            $from,
            $to,
            $this->getCurrentUser(),
            TransactionType::TYPE_COST
        );

        foreach ($transactions as $key => $transaction) {
            $notUah = $transaction->account->currency;
            $dontCalculateCosts = !$transaction->account->calculate_costs;

            if ($notUah || $dontCalculateCosts) {
                unset($transactions[$key]);
            }
        }

        return $transactions;
    }

    /**
     * Rounds the total to get exactly 2 number after the point
     *
     * @param float $sum
     * @return string
     */
    protected function roundSum(float $sum): float
    {
        return round($sum, 2);
    }
}
