<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Response;

use DateTimeImmutable;

final class ContractCreateResult
{
    public function __construct(
        private readonly string $redirectUrl,
        private readonly string $uuid,
        private readonly DateTimeImmutable $createdAt,
    ) {
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
