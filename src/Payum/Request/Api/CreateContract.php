<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Payum\Request\Api;

use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\Contract;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\Response\ContractCreateResult;

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
