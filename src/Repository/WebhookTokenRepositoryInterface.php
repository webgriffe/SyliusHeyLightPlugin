<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Repository;

use Webgriffe\SyliusPagolightPlugin\Entity\WebhookTokenInterface;

interface WebhookTokenRepositoryInterface
{
    public function add(WebhookTokenInterface $webhookToken): void;
}
