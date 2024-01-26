<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Request\Api;

use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\ApplicationStatusResult;

final class ApplicationStatus
{
    private ?ApplicationStatusResult $result = null;

    /**
     * @param string[] $contractsUuid
     */
    public function __construct(
        private readonly array $contractsUuid,
    ) {
    }

    /**
     * @return string[]
     */
    public function getContractsUuid(): array
    {
        return $this->contractsUuid;
    }

    public function getResult(): ?ApplicationStatusResult
    {
        return $this->result;
    }

    public function setResult(ApplicationStatusResult $result): void
    {
        $this->result = $result;
    }
}
