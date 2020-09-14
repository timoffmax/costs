<?php
declare(strict_types=1);

namespace App\Models\Statistic\Moneybox;

use App\Models\Statistic\TotalsAbstract;
use App\TransactionType;

/**
 * Moneybox transaction totals calculation model
 */
class GrandTotal extends TotalsAbstract
{
    /**
     * @inheritDoc
     */
    protected function getTransactionType(): string
    {
        return TransactionType::TYPE_MONEYBOX;
    }
}
