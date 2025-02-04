<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Repository;

use Sylius\Component\Core\Model\PaymentInterface;
use Webgriffe\SyliusHeylightPlugin\Entity\WebhookTokenInterface;

interface WebhookTokenRepositoryInterface
{
    public function add(WebhookTokenInterface $webhookToken): void;

    public function findOneByPayment(PaymentInterface $payment): ?WebhookTokenInterface;

    public function remove(WebhookTokenInterface $webhookToken): void;
}
