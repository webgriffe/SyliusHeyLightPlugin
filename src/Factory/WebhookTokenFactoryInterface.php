<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Factory;

use Webgriffe\SyliusHeylightPlugin\Entity\WebhookTokenInterface;

interface WebhookTokenFactoryInterface
{
    public function createNew(): WebhookTokenInterface;
}
