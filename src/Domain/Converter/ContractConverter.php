<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Domain\Converter;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\Amount;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\Config;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\Contract;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\CustomerDetails;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\RedirectUrls;
use Webmozart\Assert\Assert;

final class ContractConverter implements ContractConverterInterface
{
    public function __construct(
    ) {
    }

    public function convertFromPayment(
        PaymentInterface $payment,
        string $successUrl,
        string $failureUrl,
        ?string $cancelUrl = null,
    ): Contract {
        $currency = $payment->getCurrencyCode();
        Assert::notNull($currency);

        $order = $payment->getOrder();
        Assert::isInstanceOf($order, OrderInterface::class);

        $billingAddress = $order->getBillingAddress();

        $paymentMethod = $payment->getMethod();
        Assert::isInstanceOf($paymentMethod, PaymentMethodInterface::class);

        return new Contract(
            new Amount((string) $payment->getAmount(), $currency),
            Config::MINOR_UNIT,
            new RedirectUrls(
                $successUrl,
                $failureUrl,
                $cancelUrl,
            ),
            new CustomerDetails(
                $order->getCustomer()->getEmail(),
                null,
                $order->getCustomer()->getFirstName(),
                $order->getCustomer()->getLastName(),
                $order->getCustomer()->getBirthday(),
                $order->getCustomer()->getPhoneNumber(),
                $billingAddress?->getCompany(),
                $billingAddress?->getStreet(),
                '',
            ),
        );
    }
}
