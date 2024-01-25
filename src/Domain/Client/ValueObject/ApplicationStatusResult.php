<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject;

use InvalidArgumentException;

final class ApplicationStatusResult
{
    /**
     * @param ApplicationStatus[] $contractsStatus
     */
    public function __construct(
        private readonly array $contractsStatus,
    ) {
    }

    /**
     * @return ApplicationStatus[]
     */
    public function getContractsStatus(): array
    {
        return $this->contractsStatus;
    }

    public function getStatusByContractUuid(string $contractUuid): string
    {
        foreach ($this->contractsStatus as $contractStatus) {
            if ($contractStatus->getContractUuid() === $contractUuid) {
                return $contractStatus->getStatus();
            }
        }

        throw new InvalidArgumentException(
            sprintf('Contract with UUID "%s" not found in application status result', $contractUuid)
        );
    }
}
