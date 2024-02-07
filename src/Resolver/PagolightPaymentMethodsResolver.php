<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Resolver;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Webgriffe\SyliusPagolightPlugin\Client\Config;
use Webgriffe\SyliusPagolightPlugin\Payum\PagolightApi;
use Webmozart\Assert\Assert;

final class PagolightPaymentMethodsResolver implements PaymentMethodsResolverInterface
{
    public function __construct(
        private readonly PaymentMethodRepositoryInterface $paymentMethodRepository,
    ) {
    }

    /**
     * @param BasePaymentInterface|PaymentInterface $subject
     *
     * @return PaymentMethodInterface[]
     */
    public function getSupportedMethods(BasePaymentInterface $subject): array
    {
        Assert::true($this->supports($subject), 'This payment method is not support by resolver');
        Assert::isInstanceOf($subject, PaymentInterface::class);

        $order = $subject->getOrder();
        Assert::isInstanceOf($order, OrderInterface::class);

        $channel = $order->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);

        /** @var PaymentMethodInterface[] $paymentMethods */
        $paymentMethods = $this->paymentMethodRepository->findEnabledForChannel($channel);

        $billingAddress = $order->getBillingAddress();
        Assert::isInstanceOf($billingAddress, AddressInterface::class);
        $shippingAddress = $order->getShippingAddress();
        Assert::isInstanceOf($shippingAddress, AddressInterface::class);
        $currencyCode = $order->getCurrencyCode();
        Assert::notNull($currencyCode);
        $orderAmount = $order->getTotal();

        return array_filter(
            $paymentMethods,
            static function (PaymentMethodInterface $paymentMethod) use ($billingAddress, $shippingAddress, $currencyCode, $orderAmount) {
                $gatewayConfig = $paymentMethod->getGatewayConfig();
                if ($gatewayConfig === null) {
                    return false;
                }
                if (!in_array($gatewayConfig->getFactoryName(), [PagolightApi::PAGOLIGHT_GATEWAY_CODE, PagolightApi::PAGOLIGHT_PRO_GATEWAY_CODE], true)) {
                    return true;
                }
                if (!in_array($billingAddress->getCountryCode(), Config::ALLOWED_COUNTRY_CODES, true)) {
                    return false;
                }
                if (!in_array($shippingAddress->getCountryCode(), Config::ALLOWED_COUNTRY_CODES, true)) {
                    return false;
                }
                if (!in_array($currencyCode, Config::ALLOWED_CURRENCY_CODES, true)) {
                    return false;
                }
                if ($orderAmount <= (Config::PAGOLIGHT_PRO_MINIMUM_AMOUNT * 100) &&
                    $gatewayConfig->getFactoryName() === PagolightApi::PAGOLIGHT_PRO_GATEWAY_CODE
                ) {
                    return false;
                }

                return true;
            },
        );
    }

    public function supports(BasePaymentInterface $subject): bool
    {
        if (!$subject instanceof PaymentInterface) {
            return false;
        }
        $order = $subject->getOrder();
        if (!$order instanceof OrderInterface) {
            return false;
        }
        $channel = $order->getChannel();
        if (!$channel instanceof ChannelInterface) {
            return false;
        }
        $paymentMethod = $subject->getMethod();
        if (!$paymentMethod instanceof PaymentMethodInterface) {
            return false;
        }
        $billingAddress = $order->getBillingAddress();
        if (!$billingAddress instanceof AddressInterface) {
            return false;
        }
        $shippingAddress = $order->getShippingAddress();
        if (!$shippingAddress instanceof AddressInterface) {
            return false;
        }
        $currencyCode = $order->getCurrencyCode();

        return $currencyCode !== null;
    }
}
