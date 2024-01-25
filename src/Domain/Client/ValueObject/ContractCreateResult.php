<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject;

final class ContractCreateResult
{
    public function __construct(
        private readonly string $redirectUrl,
    ) {
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }
}
