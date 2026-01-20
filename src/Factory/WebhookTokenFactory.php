<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Factory;

use Webgriffe\SyliusHeylightPlugin\Entity\WebhookTokenInterface;
use Webmozart\Assert\Assert;

final class WebhookTokenFactory implements WebhookTokenFactoryInterface
{
    /**
     * @param class-string $webhookTokenClass
     */
    public function __construct(
        private readonly string $webhookTokenClass,
    ) {
    }

    #[\Override]
    public function createNew(): WebhookTokenInterface
    {
        /** @psalm-suppress MixedMethodCall */
        $webhookToken = new $this->webhookTokenClass();
        Assert::isInstanceOf($webhookToken, WebhookTokenInterface::class);

        return $webhookToken;
    }
}
