<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Client;

use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Webgriffe\SyliusPagolightPlugin\Client\Exception\AuthFailedException;
use Webgriffe\SyliusPagolightPlugin\Client\Exception\ClientException;

/**
 * @psalm-suppress UnusedClass
 */
final class Client implements ClientInterface
{
    public function __construct(
        private readonly PsrClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly bool $sandbox = false,
    ) {
    }

    public function auth(string $merchantKey): string
    {
        $request = $this->requestFactory->createRequest(
            'POST',
            $this->getAuthUrl(),
        );
        $request->withHeader('Content-Type', 'application/json');
        $request->withHeader('Accept', 'application/json');

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
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
                \JSON_THROW_ON_ERROR,
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

    private function getAuthUrl(): string
    {
        return sprintf('%s/auth/%s/generate', $this->getBaseUrl(), $this->getVersion());
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
