<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Payum\Request;

use Webgriffe\SyliusHeylightPlugin\Entity\WebhookTokenInterface;

final class RemovePaymentWebhookToken
{
    public function __construct(
        private readonly WebhookTokenInterface $webhookToken,
    ) {
    }

    public function getWebhookToken(): WebhookTokenInterface
    {
        return $this->webhookToken;
    }
}
