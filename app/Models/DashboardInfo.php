<?php
declare(strict_types = 1);

namespace App\Models;

use App\Models\Service\Transaction\GetByPeriod;
use App\Transaction;
use App\TransactionType;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Collects and returns some info you can use for dashboard
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

    /**
     * @var GetByPeriod
     */
    private $getByPeriod;

    /**
     * DashboardInfo constructor.
     * @param GetByPeriod $getByPeriod
     * @param array $attributes
     */
    public function __construct(
        GetByPeriod $getByPeriod,
        array $attributes = []
    ) {
        parent::__construct($attributes);

        $this->user = Auth::user();
        $this->getByPeriod = $getByPeriod;
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
     * @return array
     */
    public function getThisMonthTransactions(): array
    {
        if (is_null($this->thisMonthTransactions)) {
            $thisMonthStart = new \DateTime('first day of this month');
            $now = new \DateTime();

            $this->thisMonthTransactions = $this->getByPeriod->getByDates($thisMonthStart, $now, $this->user);
        }

        return $this->thisMonthTransactions;
    }

    /**
     * Returns all the last month transactions
     *
     * @return array
     */
    public function getLastMonthTransactions(): array
    {
        if (is_null($this->lastMonthTransactions)) {
            $lastMonthStart = new \DateTime('first day of last month');
            $lastMonthEnd = new \DateTime('last day of last month');

            $this->lastMonthTransactions = $this->getByPeriod->getByDates($lastMonthStart, $lastMonthEnd, $this->user);
        }

        return $this->lastMonthTransactions;
    }

    /**
     * Returns total sum of incomes for the provided transactions
     *
     * @param array $transactions
     * @return array
     */
    public function getTotalCosts(array $transactions): array
    {
        return $this->getTotalsByCurrency($transactions, TransactionType::TYPE_COST);
    }

    /**
     * Returns total sum of costs for the provided transactions
     *
     * @param array $transactions
     * @return array
     */
    public function getTotalIncomes(array $transactions): array
    {
        return $this->getTotalsByCurrency($transactions, TransactionType::TYPE_INCOME);
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
     * Returns totals for all currencies filtered by the transaction type
     *
     * @param array $transactions
     * @param string $type
     * @return array
     */
    public function getTotalsByCurrency(array $transactions, string $type): array
    {
        $result = [];

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            if ($type === $transaction->type->name) {
                $currency = $transaction->account->currency->sign;
                $total = $result[$currency] ?? 0.0;

                $result[$currency] = abs($this->roundSum($total + $transaction->sum));
            }
        }

        return $result;
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

    /**
     * Rounds the total to get exactly 2 number after the point
     *
     * @param float $sum
     * @return string
     */
    private function roundSum(float $sum): float
    {
        return round($sum, 2);
    }
}
