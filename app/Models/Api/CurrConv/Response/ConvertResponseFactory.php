<?php
declare(strict_types=1);

namespace App\Models\Api\CurrConv\Response;

use App\Interfaces\Api\CurrConv\Response\ConvertResponseInterface;
use App\Models\Api\CurrConv\ConvertRequest;

/**
 * Simplifies creating @see ConvertResponse object
 */
class ConvertResponseFactory
{
    /**
     * @param ConvertRequest $request
     * @param array $payload
     * @return ConvertResponseInterface
     */
    public function create(ConvertRequest $request, array $payload): ConvertResponseInterface
    {
        $result = new ConvertResponse();

        $key = "{$request->getCodeFrom()}_{$request->getCodeTo()}";
        $data = $payload[$key] ?? [];
        $result->setVal($data);

        return $result;
    }
}
