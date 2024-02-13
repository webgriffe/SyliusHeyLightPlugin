<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Entity;

use Sylius\Component\Core\Model\PaymentInterface;

/**
 * @psalm-suppress MissingConstructor
 */
class WebhookToken implements WebhookTokenInterface
{
    protected mixed $id;

    protected string $token;

    protected PaymentInterface $payment;

    public function getId(): mixed
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getPayment(): PaymentInterface
    {
        return $this->payment;
    }

    public function setPayment(PaymentInterface $payment): void
    {
        $this->payment = $payment;
    }
}
