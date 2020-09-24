<?php
declare(strict_types=1);

namespace App\Models\Api\CurrConv;

use App\Interfaces\Api\ContentTypeInterface;
use App\Interfaces\Api\CurrConv\RequestInterface;
use App\Models\Api\MethodInterface;

/**
 * Model of request that returns auth token
 */
abstract class AbstractRequest implements RequestInterface
{
    public const ENDPOINT = '/';
    public const METHOD = MethodInterface::HTTP_METHOD_GET;
    public const CONTENT_TYPE = ContentTypeInterface::TYPE_JSON;
    public const ACCEPT = ContentTypeInterface::TYPE_JSON;

    /**
     * Request parameters
     */
    protected const PARAM_API_KEY = 'apiKey';

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return static::METHOD;
    }
    /**
     * @inheritDoc
     */
    public function getEndpoint(): string
    {
        return static::ENDPOINT;
    }

    /**
     * Merges options
     * You don't need to rewrite this method in most cases
     *
     * @inheritDoc
     */
    public function getOptions(): array
    {
        $result = [];

        $result = array_merge($result, $this->getHeaders());
        $result = array_merge($result, $this->getPayload());

        return $result;
    }

    /**
     * Payload is empty by default
     *
     * @inheritDoc
     */
    public function getPayload(): array
    {
        return [];
    }

    /**
     * Most commonly required headers
     *
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        $result = [];

        $result['headers'] = [
            'Content-Type' => static::CONTENT_TYPE,
            'Accept' => static::ACCEPT,
        ];

        return $result;
    }

    /**
     * @return string
     */
    protected function getApiKey(): string
    {
        $result = env('CURR_CONV_API_KEY', '');

        return $result;
    }
}
