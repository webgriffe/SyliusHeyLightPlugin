<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Factory;

use Webgriffe\SyliusPagolightPlugin\Entity\WebhookTokenInterface;

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
        return new $this->webhookTokenClass();
    }
}
