<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Generator;

use Sylius\Component\Core\Model\PaymentInterface;
use Webgriffe\SyliusHeylightPlugin\Entity\WebhookTokenInterface;
use Webgriffe\SyliusHeylightPlugin\Factory\WebhookTokenFactoryInterface;
use Webgriffe\SyliusHeylightPlugin\Repository\WebhookTokenRepositoryInterface;

final class WebhookTokenGenerator implements WebhookTokenGeneratorInterface
{
    public function __construct(
        private readonly WebhookTokenFactoryInterface $webhookTokenFactory,
        private readonly WebhookTokenRepositoryInterface $webhookTokenRepository,
    ) {
    }

    #[\Override]
    public function generateForPayment(PaymentInterface $payment): WebhookTokenInterface
    {
        $webhookToken = $this->webhookTokenFactory->createNew();
        $webhookToken->setPayment($payment);
        $webhookToken->setToken(bin2hex(random_bytes(32)));

        $this->webhookTokenRepository->add($webhookToken);

        return $webhookToken;
    }
}
