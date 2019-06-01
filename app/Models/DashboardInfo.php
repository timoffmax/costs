<?php

declare(strict_types = 1);

namespace App\Models;

use App\Transaction;
use App\TransactionType;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Class DashboardInfo
 *
 * @property $user
 */
class DashboardInfo extends Model
{
    /**
     * @var Collection
     */
    private $thisMonthTransactions;

    /**
     * @var Collection
     */
    private $lastMonthTransactions;

    /**
     * @var User
     */
    private $user;

    public function __construct(array $attributes = [])
    {
        $this->user = Auth::user();

        parent::__construct($attributes);
    }

    /**
     * Returns all the information related to transactions
     *
     * @return array
     */
    public function getTransactionsInfo(): array
    {
        $monthTransactions = sizeof($this->getThisMonthTransactions());
        $lastMonthTransactions = sizeof($this->getLastMonthTransactions());

        $monthTotalCosts = $this->getTotalCosts($this->getThisMonthTransactions());
        $lastMonthTotalCosts = $this->getTotalCosts($this->getLastMonthTransactions());

        $monthTotalIncomes = $this->getTotalIncomes($this->getThisMonthTransactions());
        $lastMonthTotalIncomes = $this->getTotalIncomes($this->getLastMonthTransactions());

        $latestTransactions = $this->getLatestTransactionsDetails(15);

        $transactionsInfo = [
            'currentMonth' => [
                'count' => $monthTransactions,
                'costs' => $monthTotalCosts,
                'incomes' => $monthTotalIncomes,
            ],
            'lastMonth' => [
                'count' => $lastMonthTransactions,
                'costs' => $lastMonthTotalCosts,
                'incomes' => $lastMonthTotalIncomes,
            ],
            'latest' => $latestTransactions,
        ];

        return $transactionsInfo;
    }

    /**
     * Returns all the this month transactions
     *
     * @return Collection
     */
    public function getThisMonthTransactions(): Collection
    {
        if (is_null($this->thisMonthTransactions)) {
            $thisMonthStart = new \DateTime('first day of this month');
            $now = new \DateTime();

            $this->thisMonthTransactions = $this->loadTransactions($thisMonthStart, $now);
        }

        return $this->thisMonthTransactions;
    }

    /**
     * Returns all the last month transactions
     *
     * @return Collection
     */
    public function getLastMonthTransactions(): Collection
    {
        if (is_null($this->lastMonthTransactions)) {
            $lastMonthStart = new \DateTime('first day of last month');
            $lastMonthEnd = new \DateTime('last day of last month');

            $this->lastMonthTransactions = $this->loadTransactions($lastMonthStart, $lastMonthEnd);
        }

        return $this->lastMonthTransactions;
    }

    /**
     * Returns total sum of incomes for the provided transactions
     *
     * @param Collection $transactions
     * @return float
     */
    public function getTotalCosts(Collection $transactions): float
    {
        return $this->getTotalByType($transactions, TransactionType::TYPE_COST);
    }

    /**
     * Returns total sum of costs for the provided transactions
     *
     * @param Collection $transactions
     * @return float
     */
    public function getTotalIncomes(Collection $transactions): float
    {
        return $this->getTotalByType($transactions, TransactionType::TYPE_INCOME);
    }

    /**
     * Returns latest transactions
     *
     * @param int $count
     * @return array
     */
    public function getLatestTransactionsDetails(int $count): array
    {
        $transactions = Transaction::with(['account', 'type', 'category', 'place'])
            ->where('user_id', $this->user->id)
            ->latest('date')
            ->orderBy('id', 'DESC')
            ->paginate($count)
            ->items()
        ;

        return $transactions;
    }

    /**
     * Returns calculated total by transaction type
     *
     * @param Collection $transactions
     * @param string $type
     * @return float
     */
    protected function getTotalByType(Collection $transactions, string $type): float
    {
        $total = 0;

        foreach ($transactions as $transaction) {
            if ($type === $transaction->type->name) {
                $total += $transaction->sum;
            }
        }

        return $total;
    }

    /**
     * Returns all the transactions between two dates
     * (for the current user)
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @return Collection
     */
    protected function loadTransactions(\DateTime $from, \DateTime $to): Collection
    {
        $transactions = Transaction::all()
            ->whereBetween('date', [$from->format('Y-m-d'), $to->format('Y-m-d 23:59:59')])
            ->where('user_id', $this->user->id)
        ;

        return $transactions;
    }
}
