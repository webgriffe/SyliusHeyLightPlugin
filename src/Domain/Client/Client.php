<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Domain\Client;

use GuzzleHttp\ClientInterface as GuzzleHttpClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\ServerRequest;
use const JSON_THROW_ON_ERROR;
use JsonException;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\Exception\AuthFailedException;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\Exception\ClientException;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\Exception\ContractCreateFailedException;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\Contract;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\ContractCreateResult;

final class Client implements ClientInterface
{
    public function __construct(
        private readonly GuzzleHttpClientInterface $httpClient,
        private bool $sandbox = false,
    ) {
    }

    public function setSandbox(bool $isSandBox): void
    {
        $this->sandbox = $isSandBox;
    }

    public function auth(string $merchantKey): string
    {
        try {
            $bodyParams = json_encode(['merchant_key' => $merchantKey], JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new AuthFailedException(
                sprintf('Malformed auth request body: "%s".', $merchantKey),
                0,
                $e,
            );
        }
        $request = new ServerRequest(
            'POST',
            $this->getAuthUrl(),
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            $bodyParams,
        );

        try {
            $response = $this->httpClient->send($request);
        } catch (GuzzleException $e) {
            throw new ClientException($e->getMessage(), $e->getCode(), $e);
        }
        if ($response->getStatusCode() !== 200) {
            throw new AuthFailedException(
                sprintf(
                    'Unexpected auth response status code: %s - "%s".',
                    $response->getStatusCode(),
                    $response->getReasonPhrase(),
                ),
                $response->getStatusCode(),
            );
        }

        try {
            /** @var array{status: 'success', data: array{token: string}}|array{status: 'failure'} $serializedResponse */
            $serializedResponse = json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $e) {
            throw new AuthFailedException(
                sprintf(
                    'Unexpected auth response body: "%s".',
                    $response->getBody()->getContents(),
                ),
                $response->getStatusCode(),
                $e,
            );
        }
        if (!array_key_exists('status', $serializedResponse) || $serializedResponse['status'] !== 'success') {
            throw new AuthFailedException(
                sprintf(
                    'Unexpected auth response body: "%s".',
                    $response->getBody()->getContents(),
                ),
                $response->getStatusCode(),
            );
        }
        if (!array_key_exists('data', $serializedResponse) ||
            !is_array($serializedResponse['data']) ||
            !array_key_exists('token', $serializedResponse['data']) ||
            !is_string($serializedResponse['data']['token'])
        ) {
            throw new AuthFailedException(
                sprintf(
                    'Token is missing from auth response: "%s".',
                    $response->getBody()->getContents(),
                ),
                $response->getStatusCode(),
            );
        }

        return $serializedResponse['data']['token'];
    }

    public function contractCreate(Contract $contract, string $bearerToken): ContractCreateResult
    {
        try {
            $bodyParams = json_encode($contract->toArrayParams(), JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ContractCreateFailedException(
                'Malformed contract create request body.',
                0,
                $e,
            );
        }

        $request = new ServerRequest(
            'POST',
            $this->getContractCreateUrl(),
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $bearerToken,
            ],
            $bodyParams,
        );

        try {
            $response = $this->httpClient->send($request);
        } catch (GuzzleException $e) {
            throw new ClientException($e->getMessage(), $e->getCode(), $e);
        }
        if ($response->getStatusCode() !== 201) {
            throw new ContractCreateFailedException(
                sprintf(
                    'Unexpected contract create response status code: %s - "%s".',
                    $response->getStatusCode(),
                    $response->getReasonPhrase(),
                ),
                $response->getStatusCode(),
            );
        }

        try {
            /** @var array{action: 'REDIRECT', redirect_url: string, external_contract_uuid: string} $serializedResponse */
            $serializedResponse = json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $e) {
            throw new ContractCreateFailedException(
                sprintf(
                    'Malformed contract create response body: "%s".',
                    $response->getBody()->getContents(),
                ),
                $response->getStatusCode(),
                $e,
            );
        }
        if (!array_key_exists('action', $serializedResponse) || $serializedResponse['action'] !== 'REDIRECT') {
            throw new ContractCreateFailedException(
                sprintf(
                    'Unexpected contract create response body: "%s".',
                    $response->getBody()->getContents(),
                ),
                $response->getStatusCode(),
            );
        }
        if (!array_key_exists('redirect_url', $serializedResponse) ||
            !is_string($serializedResponse['redirect_url'])
        ) {
            throw new ContractCreateFailedException(
                sprintf(
                    'Redirect url is missing from contract create response: "%s".',
                    $response->getBody()->getContents(),
                ),
                $response->getStatusCode(),
            );
        }

        return new ContractCreateResult($serializedResponse['redirect_url']);
    }

    private function getAuthUrl(): string
    {
        return sprintf('%s/auth/%s/generate/', $this->getBaseUrl(), $this->getVersion());
    }

    private function getContractCreateUrl(): string
    {
        return sprintf('%s/api/checkout/%s/init/', $this->getBaseUrl(), $this->getVersion());
    }

    private function getBaseUrl(): string
    {
        return $this->sandbox ? Config::SANDBOX_BASE_URL : Config::PRODUCTION_BASE_URL;
    }

    private function getVersion(): string
    {
        return 'v1';
    }
}
