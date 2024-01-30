<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Converter;

use Sylius\Component\Core\Model\PaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Contract;

interface ContractConverterInterface
{
    public function convertFromPayment(
        PaymentInterface $payment,
        string $successUrl,
        string $failureUrl,
        ?string $cancelUrl = null,
        ?string $webhookUrl = null,
        ?string $webhookToken = null,
    ): Contract;
}
