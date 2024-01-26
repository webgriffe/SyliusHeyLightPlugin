<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject;

final class ApplicationStatus
{
    public function __construct(
        private readonly string $contractUuid,
        private readonly string $status,
    ) {
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getContractUuid(): string
    {
        return $this->contractUuid;
    }
}
