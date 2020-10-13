<?php
declare(strict_types=1);

namespace App\Interfaces\Api\CurrConv;

/**
 * Describes API request object prepared to be sent
 */
interface RequestInterface
{
    /**
     * Request method GET/POST/PUT etc.
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * API endpoint without base URI
     *
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * Merged options like payload and headers that you want to add to the request
     *
     * @return array
     */
    public function getOptions(): array;

    /**
     * Parameters you want to send in the request
     * Is merged to options array
     *
     * @return array
     */
    public function getPayload(): array;

    /**
     * Headers you want to send in the request
     * Is merged to options array
     *
     * @return array
     */
    public function getHeaders(): array;
}
