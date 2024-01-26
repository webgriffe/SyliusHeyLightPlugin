<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Request;

use Sylius\Component\Core\Model\PaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Contract;

final class ConvertPaymentToContract
{
    private ?Contract $contract = null;

    public function __construct(
        private readonly PaymentInterface $payment,
        private readonly string $successUrl,
        private readonly string $failureUrl,
        private readonly ?string $cancelUrl = null,
    ) {
    }

    public function getPayment(): PaymentInterface
    {
        return $this->payment;
    }

    public function getSuccessUrl(): string
    {
        return $this->successUrl;
    }

    public function getFailureUrl(): string
    {
        return $this->failureUrl;
    }

    public function getCancelUrl(): ?string
    {
        return $this->cancelUrl;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): void
    {
        $this->contract = $contract;
    }
}
