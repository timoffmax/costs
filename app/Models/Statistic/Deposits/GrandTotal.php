<?php
declare(strict_types=1);

namespace App\Models\Statistic\Deposits;

use App\Models\Statistic\TotalsAbstract;
use App\TransactionType;

/**
 * Deposit transaction totals calculation model
 */
class GrandTotal extends TotalsAbstract
{
    /**
     * @inheritDoc
     */
    protected function getTransactionType(): string
    {
        return TransactionType::TYPE_DEPOSIT;
    }
}
