<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Client\ValueObject;

use Webmozart\Assert\Assert;

final class Amount
{
    public function __construct(
        private readonly string $amount,
        private readonly string $currency,
    ) {
        Assert::oneOf($currency, ['CHF', 'EUR', 'GBP']);
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
