<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Request;

use Webgriffe\SyliusPagolightPlugin\Entity\WebhookTokenInterface;

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
