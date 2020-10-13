<?php
declare(strict_types=1);

namespace App\Interfaces\Api\CurrConv\Response;

use App\Interfaces\DataObjectInterface;

/**
 * Describes response of currency convert request
 */
interface ConvertResponseInterface extends DataObjectInterface
{
    public const VAL = 'val';

    /**
     * @return float|null
     */
    public function getVal(): ?float;

    /**
     * @param float $value
     * @return ConvertResponseInterface
     */
    public function setVal(float $value): ConvertResponseInterface;
}
