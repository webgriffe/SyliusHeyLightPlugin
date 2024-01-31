<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Client\ValueObject;

use Webgriffe\SyliusPagolightPlugin\Client\Config;
use Webmozart\Assert\Assert;

final class Amount
{
    public function __construct(
        private readonly string $amount,
        private readonly string $currency,
    ) {
        Assert::oneOf($currency, Config::ALLOWED_CURRENCY_CODES);
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
