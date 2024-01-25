<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Request\Api;

use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\Contract;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\ContractCreateResult;

final class CreateContract
{
    private ?ContractCreateResult $result;

    public function __construct(
        private readonly Contract $contract,
    ) {
    }

    public function getContract(): Contract
    {
        return $this->contract;
    }

    public function getResult(): ?ContractCreateResult
    {
        return $this->result;
    }
    public function setResult(ContractCreateResult $result): void
    {
        $this->result = $result;
    }
}
