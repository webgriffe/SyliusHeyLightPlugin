<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Client;

use DateTimeImmutable;
use GuzzleHttp\ClientInterface as GuzzleHttpClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\ServerRequest;
use const JSON_THROW_ON_ERROR;
use JsonException;
use Psr\Log\LoggerInterface;
use Webgriffe\SyliusPagolightPlugin\Client\Exception\ApplicationStatusFailedException;
use Webgriffe\SyliusPagolightPlugin\Client\Exception\AuthFailedException;
use Webgriffe\SyliusPagolightPlugin\Client\Exception\ClientException;
use Webgriffe\SyliusPagolightPlugin\Client\Exception\ContractCreateFailedException;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Contract;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Response\ApplicationStatus;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Response\ApplicationStatusResult;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Response\ContractCreateResult;

final class Client implements ClientInterface
{
    public function __construct(
        private readonly GuzzleHttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
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
            $message = sprintf('Malformed auth request body: "%s".', $merchantKey);
            $this->logger->error($message, ['exception' => $e]);

            throw new AuthFailedException(
                $message,
                0,
                $e,
            );
        }

        $this->logger->debug('Auth request body: ' . $bodyParams);

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
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            throw new ClientException($e->getMessage(), $e->getCode(), $e);
        }

        $this->logger->debug('Auth request response: ' . $response->getBody()->getContents());

        if ($response->getStatusCode() !== 200) {
            $message = sprintf(
                'Unexpected auth response status code: %s - "%s".',
                $response->getStatusCode(),
                $response->getReasonPhrase(),
            );
            $this->logger->error($message);

            throw new AuthFailedException(
                $message,
                $response->getStatusCode(),
            );
        }

        try {
            /** @var array{status: 'success', data: array{token: string}} $serializedResponse */
            $serializedResponse = json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $e) {
            $message = sprintf(
                'Unexpected auth response body: "%s".',
                $response->getBody()->getContents(),
            );
            $this->logger->error($message, ['exception' => $e]);

            throw new AuthFailedException(
                $message,
                $response->getStatusCode(),
                $e,
            );
        }

        return $serializedResponse['data']['token'];
    }

    public function contractCreate(Contract $contract, string $bearerToken): ContractCreateResult
    {
        try {
            $bodyParams = json_encode($contract->toArrayParams(), JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $message = 'Malformed contract create request body.';
            $this->logger->error($message, ['exception' => $e]);

            throw new ContractCreateFailedException(
                $message,
                0,
                $e,
            );
        }

        $this->logger->debug('Create contract request body: ' . $bodyParams);

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
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            throw new ClientException($e->getMessage(), $e->getCode(), $e);
        }

        $bodyContents = $response->getBody()->getContents();
        $this->logger->debug('Create contract request response: ' . $bodyContents);

        if ($response->getStatusCode() !== 201) {
            $message = sprintf(
                'Unexpected contract create response status code: %s - "%s".',
                $response->getStatusCode(),
                $response->getReasonPhrase(),
            );
            $this->logger->error($message);

            throw new ContractCreateFailedException(
                $message,
                $response->getStatusCode(),
            );
        }

        try {
            /** @var array{action: 'REDIRECT', redirect_url: string, external_contract_uuid: string} $serializedResponse */
            $serializedResponse = json_decode(
                $bodyContents,
                true,
                512,
                JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $e) {
            $message = sprintf(
                'Malformed contract create response body: "%s".',
                $bodyContents,
            );
            $this->logger->error($message, ['exception' => $e]);

            throw new ContractCreateFailedException(
                $message,
                $response->getStatusCode(),
                $e,
            );
        }

        return new ContractCreateResult(
            $serializedResponse['redirect_url'],
            $serializedResponse['external_contract_uuid'],
            new DateTimeImmutable(),
        );
    }

    public function applicationStatus(array $contractsUuid, string $bearerToken): ApplicationStatusResult
    {
        try {
            $bodyParams = json_encode(['external_contract_uuids' => $contractsUuid], JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $message = sprintf('Malformed application status request body: "%s".', implode(', ', $contractsUuid));
            $this->logger->error($message, ['exception' => $e]);

            throw new ApplicationStatusFailedException(
                $message,
                0,
                $e,
            );
        }

        $this->logger->debug('Application status request body: ' . $bodyParams);

        $request = new ServerRequest(
            'POST',
            $this->getApplicationStatusUrl(),
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
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            throw new ClientException($e->getMessage(), $e->getCode(), $e);
        }

        $this->logger->debug('Application status request response: ' . $response->getBody()->getContents());

        if ($response->getStatusCode() !== 200) {
            $message = sprintf(
                'Unexpected application status response status code: %s - "%s".',
                $response->getStatusCode(),
                $response->getReasonPhrase(),
            );
            $this->logger->error($message);

            throw new ApplicationStatusFailedException(
                $message,
                $response->getStatusCode(),
            );
        }

        try {
            /** @var array{statuses: list<array{external_contract_uuid: string, status: string}>} $serializedResponse */
            $serializedResponse = json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $e) {
            $message = sprintf(
                'Unexpected application status response body: "%s".',
                $response->getBody()->getContents(),
            );
            $this->logger->error($message, ['exception' => $e]);

            throw new ApplicationStatusFailedException(
                $message,
                $response->getStatusCode(),
                $e,
            );
        }

        $applicationStatuses = [];
        foreach ($serializedResponse['statuses'] as $applicationStatus) {
            $applicationStatuses[] = new ApplicationStatus(
                $applicationStatus['external_contract_uuid'],
                $applicationStatus['status'],
            );
        }

        return new ApplicationStatusResult($applicationStatuses);
    }

    private function getAuthUrl(): string
    {
        return sprintf('%s/auth/%s/generate/', $this->getBaseUrl(), $this->getVersion1());
    }

    private function getContractCreateUrl(): string
    {
        return sprintf('%s/api/checkout/%s/init/', $this->getBaseUrl(), $this->getVersion1());
    }

    private function getApplicationStatusUrl(): string
    {
        return sprintf('%s/api/checkout/%s/status/', $this->getBaseUrl(), $this->getVersion2());
    }

    private function getBaseUrl(): string
    {
        return $this->sandbox ? Config::SANDBOX_BASE_URL : Config::PRODUCTION_BASE_URL;
    }

    private function getVersion1(): string
    {
        return 'v1';
    }

    private function getVersion2(): string
    {
        return 'v2';
    }
}
