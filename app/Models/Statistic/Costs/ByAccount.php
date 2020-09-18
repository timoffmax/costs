<?php
declare(strict_types=1);

namespace App\Models\Statistic\Costs;

use App\Models\Statistic\StatisticAbstract;
use App\Transaction;

/**
 * Calculates costs by account
 */
class ByAccount extends StatisticAbstract
{
    /**
     * @inheritdoc
     */
    public function getInfo(string $dateFrom, string $dateTo): array
    {
        $result = [];
        $transactionsByCurrency = $this->getCostsTransactions($dateFrom, $dateTo);

        foreach ($transactionsByCurrency as $currency => $transactions) {
            /** @var Transaction $transaction */
            foreach ($transactions as $transaction) {
                $accountId = $transaction->account->id;
                $accountName = $transaction->account->name;

                $sum = $result[$accountName]['sum'] ?? 0;
                $sum += $transaction->sum;

                $accountSummary = [
                    'id' => $accountId,
                    'name' => $accountName,
                    'currency' => $currency,
                    'sum' => $this->roundSum($sum),
                ];

                $result[$accountName] = $accountSummary;
            }
        }

        ksort($result);

        return $result;
    }
}
