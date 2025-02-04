<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Client\ValueObject;

final class RedirectUrls
{
    public function __construct(
        private readonly string $successUrl,
        private readonly string $failureUrl,
        private readonly ?string $cancelUrl = null,
    ) {
    }

    public function getSuccessUrl(): string
    {
        return $this->successUrl;
    }

    public function getFailureUrl(): string
    {
        return $this->failureUrl;
    }

    public function getCancelUrl(): ?string
    {
        return $this->cancelUrl;
    }
}
