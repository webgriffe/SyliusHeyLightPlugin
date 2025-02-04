<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Payum\Request\Api;

use Webgriffe\SyliusHeylightPlugin\Payum\HeylightApi;

final class Auth
{
    private ?string $bearerToken = null;

    public function __construct(private readonly HeylightApi $heylightApi)
    {
    }

    public function getHeylightApi(): HeylightApi
    {
        return $this->heylightApi;
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
