<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Request\Api;

use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Contract;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Response\ContractCreateResult;

final class CreateContract
{
    private ?ContractCreateResult $result = null;

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
