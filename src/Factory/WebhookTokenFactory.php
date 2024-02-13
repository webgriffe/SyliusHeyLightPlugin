<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Factory;

use Webgriffe\SyliusPagolightPlugin\Entity\WebhookTokenInterface;
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

    public function createNew(): WebhookTokenInterface
    {
        /** @psalm-suppress MixedMethodCall */
        $webhookToken = new $this->webhookTokenClass();
        Assert::isInstanceOf($webhookToken, WebhookTokenInterface::class);

        return $webhookToken;
    }
}
