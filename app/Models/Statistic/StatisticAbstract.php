<?php
declare(strict_types=1);

namespace App\Models\Statistic;

use App\Models\Service\Currency\GetCourses;
use App\Models\Service\Transaction\GetByPeriod;
use App\Models\Traits\User\CurrentUserTrait;
use App\Transaction;
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
     * @var GetCourses
     */
    private $getCourses;

    /**
     * StatisticAbstract constructor.
     * @param GetByPeriod $getByPeriod
     * @param GetCourses $getCourses
     */
    public function __construct(GetByPeriod $getByPeriod, GetCourses $getCourses)
    {
        $this->getByPeriod = $getByPeriod;
        $this->getCourses = $getCourses;
    }

    /**
     * Returns transactions for the specified period
     *
     * @param string $from
     * @param string $to
     * @param string|null $type
     * @return array
     * @throws \Exception
     */
    protected function getTransactions(string $from, string $to, ?string $type = null): array
    {
        $user = $this->getCurrentUser();
        $transactions = $this->getByPeriod->getByStringDates($from, $to, $user, $type);

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
        $result = [];
        $transactions = $this->getTransactions($from, $to, TransactionType::TYPE_COST);

        /** @var Transaction $transaction */
        foreach ($transactions as $key => $transaction) {
            $currency = $transaction->account->currency->sign;
            $dontCalculateCosts = !$transaction->account->calculate_costs;

            if (true === $dontCalculateCosts) {
                continue;
            }

            $result[$currency][] = $transaction;
        }

        return $result;
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

    /**
     * @return float[]
     */
    protected function getCurrencyCourses(): array
    {
        $result = $this->getCourses->bySign();

        return $result;
    }
}
