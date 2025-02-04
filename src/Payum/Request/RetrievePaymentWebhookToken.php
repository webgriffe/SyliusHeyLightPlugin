<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Payum\Request;

use Sylius\Component\Core\Model\PaymentInterface;
use Webgriffe\SyliusHeylightPlugin\Entity\WebhookTokenInterface;

final class RetrievePaymentWebhookToken
{
    private ?WebhookTokenInterface $webhookToken = null;

    public function __construct(
        private readonly PaymentInterface $payment,
    ) {
    }

    public function getPayment(): PaymentInterface
    {
        return $this->payment;
    }

    public function getWebhookToken(): ?WebhookTokenInterface
    {
        return $this->webhookToken;
    }

    public function setWebhookToken(?WebhookTokenInterface $webhookToken): void
    {
        $this->webhookToken = $webhookToken;
    }
}
