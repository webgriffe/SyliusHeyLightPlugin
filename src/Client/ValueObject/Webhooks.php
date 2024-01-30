<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Client\ValueObject;

final class Webhooks
{
    private string $mappingScheme = 'DEFAULT';

    public function __construct(
        private readonly string $statusUrl,
        private readonly string $token,
    ) {
    }

    public function getStatusUrl(): string
    {
        return $this->statusUrl;
    }

    public function getMappingScheme(): string
    {
        return $this->mappingScheme;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
