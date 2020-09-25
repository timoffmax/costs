<?php
declare(strict_types=1);

namespace App\Models\Api\CurrConv;

use App\Interfaces\Api\CurrConv\RequestInterface;
use GuzzleHttp\RequestOptions;

/**
 * API request that returns currency convert rate
 */
class ConvertRequest extends AbstractRequest implements RequestInterface
{
    public const ENDPOINT = 'convert';

    /**
     * Request parameters
     */
    private const PARAM_QUERY = 'q';
    private const PARAM_COMPACT = 'compact';

    /**
     * @var string
     */
    private $codeFrom;

    /**
     * @var string
     */
    private $codeTo;

    /**
     * ConvertRequest constructor.
     * @param string $codeFrom
     * @param string $codeTo
     */
    public function __construct(string $codeFrom, string $codeTo)
    {
        $this->codeFrom = $codeFrom;
        $this->codeTo = $codeTo;
    }

    /**
     * @inheritdoc
     */
    public function getPayload(): array
    {
        $result = [];
        $paramQuery = "{$this->codeFrom}_{$this->codeTo}";

        $result[RequestOptions::QUERY] = [
            static::PARAM_API_KEY => $this->getApiKey(),
            static::PARAM_QUERY => $paramQuery,
            static::PARAM_COMPACT => 'ultra',
        ];

        return $result;
    }

    /**
     * @return string
     */
    public function getCodeFrom(): string
    {
        return $this->codeFrom;
    }

    /**
     * @return string
     */
    public function getCodeTo(): string
    {
        return $this->codeTo;
    }
}
