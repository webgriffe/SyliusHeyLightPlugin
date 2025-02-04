<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Entity;

use Sylius\Component\Core\Model\PaymentInterface;

interface WebhookTokenInterface
{
    public function getId(): mixed;

    public function getToken(): string;

    public function setToken(string $token): void;

    public function getPayment(): PaymentInterface;

    public function setPayment(PaymentInterface $payment): void;
}
