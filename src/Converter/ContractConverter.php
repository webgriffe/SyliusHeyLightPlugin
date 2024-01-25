<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Converter;

use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
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
        TokenInterface $captureToken,
        ?TokenInterface $webhookToken = null,
    ): Contract {
        $currency = $payment->getCurrencyCode();
        Assert::notNull($currency);

        $order = $payment->getOrder();
        Assert::isInstanceOf($order, OrderInterface::class);

        $billingAddress = $order->getBillingAddress();

        $syliusCaptureUrl = $captureToken->getTargetUrl();
        $syliusAfterUrl = $captureToken->getAfterUrl();

        return new Contract(
            new Amount((string) $payment->getAmount(), $currency),
            Config::MINOR_UNIT,
            new RedirectUrls(
                $syliusCaptureUrl,
                $syliusAfterUrl,
                $syliusAfterUrl,
            ),
            new CustomerDetails(
                $order->getCustomer()->getEmail(),
                $order->getCustomer()->getFirstName(),
                $order->getCustomer()->getLastName(),
                $order->getCustomer()->getPhoneNumber(),
                $order->getCustomer()->getBirthday(),
                $order->getCustomer()->getPhoneNumber(),
                $billingAddress?->getCompany(),
                $billingAddress?->getStreet(),
                '',
            ),
        );
    }
}
