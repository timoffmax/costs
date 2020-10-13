<?php
declare(strict_types=1);

namespace App\Models\Service\Api\CurrConv\Request;

use App\Exceptions\Api\CurrConv\CurrConvException;
use App\Interfaces\Api\CurrConv\Response\ConvertResponseInterface;
use App\Models\Api\CurrConv\ConvertRequest;
use App\Models\Api\CurrConv\Response\ConvertResponseFactory;
use App\Models\Service\Api\CurrConv\SendRequest;
use Illuminate\Support\Facades\Log;

/**
 * Sugar-service that sends @see ConvertRequest
 */
class Convert
{
    /**
     * @var SendRequest
     */
    private $sendRequest;

    /**
     * @var ConvertResponseFactory
     */
    private $responseFactory;

    /**
     * Convert constructor.
     * @param SendRequest $sendRequest
     * @param ConvertResponseFactory $responseFactory
     */
    public function __construct(
        SendRequest $sendRequest,
        ConvertResponseFactory $responseFactory
    ) {
        $this->sendRequest = $sendRequest;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param string $codeFrom
     * @param string $codeTo
     * @return ConvertResponseInterface
     * @throws CurrConvException
     */
    public function execute(string $codeFrom, string $codeTo): ConvertResponseInterface
    {
        $request = new ConvertRequest($codeFrom, $codeTo);

        try {
            $response = $this->sendRequest->execute($request);
            $result = $this->processSuccess($request, $response);
        } catch (CurrConvException $e) {
            $this->processError($e);
        }

        return $result;
    }

    /**
     * @param ConvertRequest $request
     * @param array $response
     * @return ConvertResponseInterface
     */
    private function processSuccess(ConvertRequest $request, array $response): ConvertResponseInterface
    {
        $result = $this->responseFactory->create($request, $response);

        return $result;
    }

    /**
     * @param CurrConvException $exception
     * @throws CurrConvException
     */
    private function processError(CurrConvException $exception): void
    {
        $message = "CurrConv convert API request failed. Error: {$exception->getMessage()}";
        Log::error($message);

        throw new CurrConvException(__($exception->getMessage()));
    }
}
