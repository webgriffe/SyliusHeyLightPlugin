<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Entity;

use Sylius\Component\Core\Model\PaymentInterface;

/**
 * @psalm-suppress MissingConstructor
 * @psalm-suppress ClassMustBeFinal
 */
class WebhookToken implements WebhookTokenInterface
{
    protected mixed $id;

    protected string $token;

    protected PaymentInterface $payment;

    #[\Override]
    public function getId(): mixed
    {
        return $this->id;
    }

    #[\Override]
    public function getToken(): string
    {
        return $this->token;
    }

    #[\Override]
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    #[\Override]
    public function getPayment(): PaymentInterface
    {
        return $this->payment;
    }

    #[\Override]
    public function setPayment(PaymentInterface $payment): void
    {
        $this->payment = $payment;
    }
}
