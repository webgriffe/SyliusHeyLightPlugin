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

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getContractUuid(): string
    {
        return $this->contractUuid;
    }
}
