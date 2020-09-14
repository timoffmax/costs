<?php
declare(strict_types=1);

namespace App\Models\Statistic;

use App\Models\Service\Transaction\GetByPeriod;
use App\Models\Traits\User\CurrentUserTrait;
use App\Transaction;
use App\TransactionType;

/**
 * Abstract model for calculating totals
 */
abstract class TotalsAbstract implements TotalsInterface
{
    use CurrentUserTrait;

    /**
     * @var GetByPeriod
     */
    protected $getByPeriod;

    /**
     * TotalsAbstract constructor.
     * @param GetByPeriod $getByPeriod
     */
    public function __construct(GetByPeriod $getByPeriod)
    {
        $this->getByPeriod = $getByPeriod;
    }

    /**
     * You must specify transaction type to filter by it
     *
     * @return string
     */
    abstract protected function getTransactionType(): string;

    /**
     * Returns totals sum of money temporarily put off to spend them in the future
     *
     * @inheritDoc
     * @throws \Exception
     */
    public function getTotals(string $dateFrom, string $dateTo): float
    {
        $result = 0.0;

        $type = $this->getTransactionType();
        $transactions = $this->getTransactionsByType($dateFrom, $dateTo, $type);

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $result += abs($transaction->sum);
        }

        $result = $this->roundSum($result);

        return $result;
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     * @throws \Exception
     */
    public function getTotalsByCurrency(string $dateFrom, string $dateTo): array
    {
        $result = [];

        $type = $this->getTransactionType();
        $transactions = $this->getTransactionsByType($dateFrom, $dateTo, $type);

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $currency = $transaction->account->currency->sign;
            $total = $result[$currency] ?? 0.0;

            $result[$currency] = abs($this->roundSum($total + $transaction->sum));
        }

        return $result;
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
        return $this->getTransactionsByType($from, $to, TransactionType::TYPE_COST);
    }

    /**
     * Returns only incomes transactions
     *
     * @param string $from
     * @param string $to
     * @return array
     * @throws \Exception
     */
    protected function getIncomeTransactions(string $from, string $to): array
    {
        return $this->getTransactionsByType($from, $to, TransactionType::TYPE_INCOME);
    }

    /**
     * Returns only deposit transactions
     *
     * @param string $from
     * @param string $to
     * @return array
     * @throws \Exception
     */
    protected function getDepositTransactions(string $from, string $to): array
    {
        return $this->getTransactionsByType($from, $to, TransactionType::TYPE_DEPOSIT);
    }

    /**
     * Returns only moneybox transactions
     *
     * @param string $from
     * @param string $to
     * @return array
     * @throws \Exception
     */
    protected function getMoneyboxTransactions(string $from, string $to): array
    {
        return $this->getTransactionsByType($from, $to, TransactionType::TYPE_MONEYBOX);
    }

    /**
     * Returns only saving transactions
     *
     * @param string $from
     * @param string $to
     * @return array
     * @throws \Exception
     */
    protected function getSavingTransactions(string $from, string $to): array
    {
        return $this->getTransactionsByTypeAndCurrency($from, $to, TransactionType::TYPE_SAVING);
    }

    /**
     * Returns transactions of the specified type
     * Filters out inappropriate ones
     *
     * @param string $from
     * @param string $to
     * @param string $type
     * @return array
     * @throws \Exception
     */
    protected function getTransactionsByType(string $from, string $to, string $type): array
    {
        $transactions = $this->getByPeriod->getByStringDates(
            $from,
            $to,
            $this->getCurrentUser(),
            $type
        );

        foreach ($transactions as $key => $transaction) {
            $dontCalculateCosts = !$transaction->account->calculate_costs;

            if (true === $dontCalculateCosts) {
                unset($transactions[$key]);
            }
        }

        return $transactions;
    }

    /**
     * @param string $from
     * @param string $to
     * @param string $type
     * @return array
     * @throws \Exception
     */
    protected function getTransactionsByTypeAndCurrency(string $from, string $to, string $type): array
    {
        $transactions = $this->getByPeriod->getByStringDates(
            $from,
            $to,
            $this->getCurrentUser(),
            $type
        );

        foreach ($transactions as $key => $transaction) {
            $dontCalculateCosts = (false === (bool)$transaction->account->calculate_costs);

            if ($dontCalculateCosts) {
                unset($transactions[$key]);
            }
        }

        return $transactions;
    }

    /**
     * Format the total
     *
     * @param float $sum
     * @return string
     */
    protected function formatSum(float $sum): string
    {
        return number_format((float)$sum, 2, '.', '');
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
