<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Converter;

use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Contract;

interface ContractConverterInterface
{
    public function convertFromPayment(
        PaymentInterface $payment,
        TokenInterface $captureToken,
        ?TokenInterface $webhookToken = null,
    ): Contract;
}
