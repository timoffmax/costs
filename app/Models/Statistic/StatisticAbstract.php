<?php
declare(strict_types=1);

namespace App\Models\Statistic;

use App\Models\Service\Transaction\GetByPeriod;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Class StatisticAbstract
 */
abstract class StatisticAbstract implements StatisticInterface
{
    /**
     * @var GetByPeriod
     */
    protected $getByPeriod;

    /**
     * Chart constructor.
     * @param GetByPeriod $getByPeriod
     */
    public function __construct(
        GetByPeriod $getByPeriod
    ) {
        $this->getByPeriod = $getByPeriod;
    }

    /**
     * Returns transactions for the specified period
     *
     * @return Collection
     * @throws \Exception
     */
    protected function getTransactions(string $from, string $to): Collection
    {
        $startDate = new \DateTime($from);
        $endDate = new \DateTime($to);

        /** @var User $user */
        $user = Auth::user();
        $transactions = $this->getByPeriod->execute($startDate, $endDate, $user);

        return $transactions;
    }
}
