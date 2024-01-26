<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Converter;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webgriffe\SyliusPagolightPlugin\Client\Config;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Amount;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Contract;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\CustomerDetails;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\RedirectUrls;
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

        $customer = $order->getCustomer();
        Assert::isInstanceOf($customer, CustomerInterface::class);

        $emailAddress = $customer->getEmail();
        Assert::stringNotEmpty($emailAddress, 'Email is required to create a contract on Pagolight');

        return new Contract(
            new Amount((string) $payment->getAmount(), $currency),
            Config::MINOR_UNIT,
            new RedirectUrls(
                $successUrl,
                $failureUrl,
                $cancelUrl,
            ),
            new CustomerDetails(
                $emailAddress,
                null,
                $customer->getFirstName(),
                $customer->getLastName(),
                $customer->getBirthday(),
                $customer->getPhoneNumber(),
                $billingAddress?->getCompany(),
                $billingAddress?->getStreet(),
                '',
            ),
        );
    }
}
