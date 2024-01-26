<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Request\Api;

use Webgriffe\SyliusPagolightPlugin\Payum\PagolightApi;

final class Auth
{
    private ?string $bearerToken = null;

    public function __construct(private readonly PagolightApi $pagolightApi)
    {
    }

    public function getPagolightApi(): PagolightApi
    {
        return $this->pagolightApi;
    }

    public function getBearerToken(): ?string
    {
        return $this->bearerToken;
    }

    public function setBearerToken(string $bearerToken): void
    {
        $this->bearerToken = $bearerToken;
    }
}
