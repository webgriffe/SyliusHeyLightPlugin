<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Converter;

use Sylius\Component\Core\Model\PaymentInterface;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\Contract;

interface ContractConverterInterface
{
    /**
     * @param int[] $allowedTerms
     * @param array<string, string> $additionalData
     */
    public function convertFromPayment(
        PaymentInterface $payment,
        string $successUrl,
        string $failureUrl,
        ?string $cancelUrl = null,
        ?string $webhookUrl = null,
        ?string $webhookToken = null,
        array $allowedTerms = [],
        array $additionalData = [],
    ): Contract;
}
