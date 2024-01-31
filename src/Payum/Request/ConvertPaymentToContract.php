<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Request;

use Sylius\Component\Core\Model\PaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Contract;

final class ConvertPaymentToContract
{
    private ?Contract $contract = null;

    /**
     * @param int[] $allowedTerms
     * @param array<string, string> $additionalData
     */
    public function __construct(
        private readonly PaymentInterface $payment,
        private readonly string $successUrl,
        private readonly string $failureUrl,
        private readonly ?string $cancelUrl = null,
        private readonly ?string $webhookUrl = null,
        private readonly ?string $webhookToken = null,
        private readonly array $allowedTerms = [],
        private readonly array $additionalData = [],
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

    public function getWebhookUrl(): ?string
    {
        return $this->webhookUrl;
    }

    public function getWebhookToken(): ?string
    {
        return $this->webhookToken;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): void
    {
        $this->contract = $contract;
    }

    /**
     * @return int[]
     */
    public function getAllowedTerms(): array
    {
        return $this->allowedTerms;
    }

    /**
     * @return array<string, string>
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }
}
