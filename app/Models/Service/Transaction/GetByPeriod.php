<?php
declare(strict_types=1);

namespace App\Models\Service\Transaction;

use App\Transaction;
use App\TransactionType;
use App\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class GetByPeriod
 */
class GetByPeriod
{
    /**
     * @var array
     */
    private $cache = [];

    /**
     * Returns transactions for the specified period
     * Accepts date objects as arguments
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @param User $user
     * @return array
     */
    public function getByDates(\DateTime $from, \DateTime $to, User $user, ?string $type = null): array
    {
        $fromDate = $from->format('Y-m-d');
        $toDate = $to->format('Y-m-d 23:59:59');
        $cacheKey = "{$type}_{$fromDate}_{$toDate}";

        $cachedValue = $this->getFromCache($cacheKey);

        if (!is_null($cachedValue)) {
            return $cachedValue;
        }

        $transactions = $this->getTransactionsByType($fromDate, $toDate, $user, $type);
        $this->setCache($cacheKey, $transactions);

        return $transactions;
    }

    /**
     * Returns transactions for the specified period
     * Accepts date stings as arguments
     *
     * @param string $from
     * @param string $to
     * @param User $user
     * @param string|null $type
     * @return array
     * @throws \Exception
     */
    public function getByStringDates(string $from, string $to, User $user, ?string $type = null): array
    {
        $startDate = new \DateTime($from);
        $endDate = new \DateTime($to);

        return $this->getByDates($startDate, $endDate, $user, $type);
    }

    /**
     * Returns cached value if exists
     *
     * @param string $cacheKey
     * @return array|null
     */
    private function getFromCache(string $cacheKey): ?array
    {
        return $this->cache[$cacheKey] ?? null;
    }

    /**
     * Caches the value
     *
     * @param $cacheKey
     * @param Collection $transactions
     */
    private function setCache($cacheKey, array $transactions): void
    {
        $this->cache[$cacheKey] = $transactions;
    }

    /**
     * Returns only filtered by type transactions between two dates for the specified user
     *
     * @param string $from
     * @param string $to
     * @param User $user
     * @return array
     */
    private function getTransactionsByType(string $from, string $to, User $user, ?string $type): array
    {
        $transactions = Transaction:: whereBetween(Transaction::DATE, [$from, $to])
            ->where(Transaction::USER_ID, $user->id);

        if (null !== $type) {
            /** @var TransactionType $transactionType */
            $transactionType = TransactionType::whereName($type)->first();
            $transactions->where(Transaction::TYPE_ID, $transactionType->id);
        }

        return $transactions->getModels();
    }
}
