<?php
declare(strict_types=1);

namespace App\Models\Api\CurrConv;

/**
 * CurrConv API config model
 */
class Config
{
    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        $result = $result = env('CURR_CONV_API_URL', '');

        return $result;
    }
}
