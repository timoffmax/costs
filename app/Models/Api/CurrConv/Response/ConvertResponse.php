<?php
declare(strict_types=1);

namespace App\Models\Api\CurrConv\Response;

use App\Interfaces\Api\CurrConv\Response\ConvertResponseInterface;
use App\Models\DataObject;

/**
 * Convert response payload data object
 */
class ConvertResponse extends DataObject implements ConvertResponseInterface
{
    /**
     * @inheritdoc
     */
    public function getVal(): ?float
    {
        return $this->getData(ConvertResponseInterface::VAL);
    }

    /**
     * @inheritdoc
     */
    public function setVal(float $value): ConvertResponseInterface
    {
        return $this->setData(ConvertResponseInterface::VAL, $value);
    }
}
