<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Repository;

use Sylius\Component\Core\Model\PaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Entity\WebhookTokenInterface;

interface WebhookTokenRepositoryInterface
{
    public function add(WebhookTokenInterface $webhookToken): void;

    public function findOneByPayment(PaymentInterface $payment): ?WebhookTokenInterface;

    public function remove(WebhookTokenInterface $webhookToken): void;
}
