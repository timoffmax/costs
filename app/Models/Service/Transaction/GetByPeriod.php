<?php

namespace App\Models\Service\Transaction;

use App\Transaction;
use App\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class GetByPeriod
 */
class GetByPeriod
{
    /**
     * @var Collection[]
     */
    private $cache = [];

    /**
     * Returns transactions fo the specified period
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @param User $user
     * @return Collection
     */
    public function execute(\DateTime $from, \DateTime $to, User $user): Collection
    {
        $fromDate = $from->format('Y-m-d');
        $toDate = $to->format('Y-m-d 23:59:59');
        $cacheKey = "{$fromDate}_{$toDate}";

        $cachedValue = $this->getFromCache($cacheKey);

        if (!is_null($cachedValue)) {
            return $cachedValue;
        }

        $transactions = $this->getTransactions($fromDate, $toDate, $user);
        $this->setCache($cacheKey, $transactions);

        return $transactions;
    }

    /**
     * Returns cached value if exists
     *
     * @param string $cacheKey
     * @return Collection|null
     */
    private function getFromCache(string $cacheKey): ?Collection
    {
        return $this->cache[$cacheKey] ?? null;
    }

    /**
     * Caches the value
     *
     * @param $cacheKey
     * @param Collection $transactions
     */
    private function setCache($cacheKey, Collection $transactions): void
    {
        $this->cache[$cacheKey] = $transactions;
    }

    /**
     * Returns all existed transactions between two dates for the current user
     *
     * @param string $from
     * @param string $to
     * @param User $user
     * @return Collection
     */
    private function getTransactions(string $from, string $to, User $user): Collection
    {
        $transactions = Transaction::all()
            ->whereBetween('date', [$from, $to])
            ->where('user_id', $user->id)
        ;

        return $transactions;
    }
}
