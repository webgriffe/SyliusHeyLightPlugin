<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Generator;

use Sylius\Component\Core\Model\PaymentInterface;
use Webgriffe\SyliusHeylightPlugin\Entity\WebhookTokenInterface;

interface WebhookTokenGeneratorInterface
{
    public function generateForPayment(PaymentInterface $payment): WebhookTokenInterface;
}
