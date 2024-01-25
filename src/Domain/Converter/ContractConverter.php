<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Domain\Converter;

use Payum\Core\Model\GatewayConfigInterface;
use Payum\Core\Payum;
use Payum\Core\Security\TokenInterface;
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
        private readonly Payum $payum,
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

        $paymentMethod = $payment->getMethod();
        Assert::isInstanceOf($paymentMethod, PaymentMethodInterface::class);

        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();

        $tokenFactory = $this->payum->getTokenFactory();
        $cancelToken = $tokenFactory->createToken(
            $gatewayConfig->getGatewayName(),
            $payment,
            'webgriffe_sylius_pagolight_cancel_payment',
            [],
            $captureToken->getTargetUrl(),
        );
        $failToken = $tokenFactory->createToken(
            $gatewayConfig->getGatewayName(),
            $payment,
            'webgriffe_sylius_pagolight_fail_payment',
            [],
            $captureToken->getTargetUrl(),
        );

        return new Contract(
            new Amount((string) $payment->getAmount(), $currency),
            Config::MINOR_UNIT,
            new RedirectUrls(
                $captureToken->getTargetUrl(),
                $failToken->getTargetUrl(),
                $cancelToken->getTargetUrl(),
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
