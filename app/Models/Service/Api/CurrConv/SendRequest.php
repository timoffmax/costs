<?php
declare(strict_types=1);

namespace App\Models\Service\Api\CurrConv;

use App\Exceptions\Api\CurrConv\CurrConvException;
use App\Interfaces\Api\CurrConv\RequestInterface;
use App\Models\Api\CurrConv\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;

/**
 * Unified CurrConv API request sender
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SendRequest
{
    /**
     * Failure processing settings
     */
    private const MAX_ATTEMPTS = 5;
    private static $attemptNumber = 0;

    /**
     * @var Config
     */
    private $config;

    /**
     * SendRequest constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param RequestInterface $requestObject
     * @return array
     * @throws CurrConvException
     */
    public function execute(RequestInterface $requestObject): array
    {
        $response = $this->sendRequest($requestObject);
        $result = $this->processResponse($response, $requestObject);

        return $result;
    }

    /**
     * @param RequestInterface $requestObject
     * @return Response
     */
    private function sendRequest(RequestInterface $requestObject): Response
    {
        $client = $this->getClient();

        try {
            $response = $client->request(
                $requestObject->getMethod(),
                $requestObject->getEndpoint(),
                $requestObject->getOptions()
            );
        } catch (GuzzleException $exception) {
            /** @var Response $response */
            $response = new Response([
                'status' => (int)$exception->getCode(),
                'reason' => $exception->getMessage()
            ]);

            $message = "API request is failed with status {$response->getStatusCode()}.";
            $message .= " Reason: {$response->getReasonPhrase()}.";
            Log::error($message);
        }

        return $response;
    }

    /**
     * @param Response $response
     * @param RequestInterface $requestObject
     * @return array
     * @throws CurrConvException
     */
    private function processResponse(Response $response, RequestInterface $requestObject): array
    {
        $isSuccess = in_array($response->getStatusCode(), [200, 201]);

        if (true === $isSuccess) {
            $result = $this->processSuccess($response);
        } else {
            $result = $this->processRetry($requestObject, $response);
        }

        return $result;
    }

    /**
     * @param Response $response
     * @return array
     */
    private function processSuccess(Response $response): array
    {
        $responseJson = $response->getBody()->getContents();
        $result = json_decode($responseJson, true);

        return $result;
    }

    /**
     * @param Response $response
     * @throws CurrConvException
     */
    private function processError(Response $response): void
    {
        $message = "Request failed with status code {$response->getStatusCode()}. ";
        $message .= "Details: {$response->getReasonPhrase()}.";

        throw new CurrConvException($message);
    }

    /**
     * @param RequestInterface $requestObject
     * @param Response $response
     * @return array
     * @throws CurrConvException
     */
    private function processRetry(RequestInterface $requestObject, Response $response): array
    {
        if ($this::$attemptNumber >= static::MAX_ATTEMPTS) {
            $message = "Too many attempts: {$this::$attemptNumber}. Last error: {$response->getReasonPhrase()}";
            Log::error($message);

            $this->processError($response);
        }

        $this::$attemptNumber++;
        $result = $this->execute($requestObject);

        return $result;
    }

    /**
     * Sets up the client object and returns it
     *
     * @return Client
     */
    private function getClient(): Client
    {
        /** @var Client $client */
        $client = new Client([
            'base_uri' => $this->config->getApiUrl(),
        ]);

        return $client;
    }
}
