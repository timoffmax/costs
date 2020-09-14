<?php
declare(strict_types=1);

namespace App\Models\Statistic\Costs;

use App\Models\Statistic\TotalsAbstract;
use App\TransactionType;

/**
 * Cost transaction totals calculation model
 */
class GrandTotal extends TotalsAbstract
{
    /**
     * @inheritDoc
     */
    protected function getTransactionType(): string
    {
        return TransactionType::TYPE_COST;
    }
}
