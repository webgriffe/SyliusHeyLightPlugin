<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Factory;

use Webgriffe\SyliusPagolightPlugin\Entity\WebhookTokenInterface;

interface WebhookTokenFactoryInterface
{
    public function createNew(): WebhookTokenInterface;
}
