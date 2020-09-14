<?php
declare(strict_types=1);

namespace App\Models\Statistic\Savings;

use App\Models\Statistic\TotalsAbstract;
use App\TransactionType;

/**
 * Saving transaction totals calculation model
 */
class GrandTotal extends TotalsAbstract
{
    /**
     * @inheritDoc
     */
    protected function getTransactionType(): string
    {
        return TransactionType::TYPE_SAVING;
    }
}
