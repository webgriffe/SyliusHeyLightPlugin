<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Domain\Converter;

use Sylius\Component\Core\Model\PaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\Contract;

interface ContractConverterInterface
{
    public function convertFromPayment(
        PaymentInterface $payment,
        string $successUrl,
        string $failureUrl,
        ?string $cancelUrl = null,
    ): Contract;
}
