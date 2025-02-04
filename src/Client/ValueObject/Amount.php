<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Client\ValueObject;

use Webgriffe\SyliusHeylightPlugin\Client\Config;
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
