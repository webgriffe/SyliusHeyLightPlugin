<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Generator;

use Sylius\Component\Core\Model\PaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Entity\WebhookTokenInterface;

interface WebhookTokenGeneratorInterface
{
    public function generateForPayment(PaymentInterface $payment): WebhookTokenInterface;
}
