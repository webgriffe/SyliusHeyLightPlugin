<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusHeylightPlugin\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Webgriffe\SyliusHeylightPlugin\Payum\HeylightApi;
use Webgriffe\SyliusHeylightPlugin\Resolver\HeylightPaymentMethodsResolver;

final class HeylightPaymentMethodsResolverSpec extends ObjectBehavior
{
    public function let(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentInterface $payment,
        PaymentMethodInterface $heylightPaymentMethod,
        PaymentMethodInterface $heylightProPaymentMethod,
        PaymentMethodInterface $otherPaymentMethod,
        OrderInterface $order,
        ChannelInterface $channel,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        GatewayConfigInterface $heylightBnplGatewayConfig,
        GatewayConfigInterface $heylightFinancingGatewayConfig,
        GatewayConfigInterface $otherGatewayConfig,
    ): void {
        $billingAddress->getCountryCode()->willReturn('IT');
        $shippingAddress->getCountryCode()->willReturn('IT');

        $order->getChannel()->willReturn($channel);
        $order->getBillingAddress()->willReturn($billingAddress);
        $order->getShippingAddress()->willReturn($shippingAddress);
        $order->getCurrencyCode()->willReturn('EUR');
        $order->getTotal()->willReturn(19000);

        $payment->getOrder()->willReturn($order);
        $payment->getMethod()->willReturn($heylightPaymentMethod);

        $heylightBnplGatewayConfig->getFactoryName()->willReturn(HeylightApi::HEYLIGHT_BNPL_GATEWAY_CODE);
        $heylightBnplGatewayConfig->getConfig()->willReturn(['allowed_terms' => []]);
        $heylightFinancingGatewayConfig->getFactoryName()->willReturn(HeylightApi::HEYLIGHT_FINANCING_GATEWAY_CODE);
        $heylightFinancingGatewayConfig->getConfig()->willReturn(['allowed_terms' => []]);
        $otherGatewayConfig->getFactoryName()->willReturn('other');

        $heylightPaymentMethod->getCode()->willReturn('HEYLIGHT_PAYMENT_METHOD_CODE');
        $heylightPaymentMethod->getGatewayConfig()->willReturn($heylightBnplGatewayConfig);
        $heylightProPaymentMethod->getCode()->willReturn('HEYLIGHT_PRO_PAYMENT_METHOD_CODE');
        $heylightProPaymentMethod->getGatewayConfig()->willReturn($heylightFinancingGatewayConfig);
        $otherPaymentMethod->getCode()->willReturn('other_payment_method');
        $otherPaymentMethod->getGatewayConfig()->willReturn($otherGatewayConfig);

        $paymentMethodRepository->findEnabledForChannel($channel)->willReturn([
            $heylightPaymentMethod,
            $heylightProPaymentMethod,
            $otherPaymentMethod,
        ]);

        $this->beConstructedWith($paymentMethodRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(HeylightPaymentMethodsResolver::class);
    }

    public function it_implements_payment_methods_resolver_interface(): void
    {
        $this->shouldImplement(PaymentMethodsResolverInterface::class);
    }

    public function it_resolves_heylight_payment_methods_if_eligible(
        PaymentInterface $payment,
        PaymentMethodInterface $heylightPaymentMethod,
        PaymentMethodInterface $heylightProPaymentMethod,
        PaymentMethodInterface $otherPaymentMethod,
    ): void {
        $this->getSupportedMethods($payment)->shouldReturn([
            0 => $heylightPaymentMethod,
            1 => $heylightProPaymentMethod,
            2 => $otherPaymentMethod,
        ]);
    }

    public function it_does_not_resolve_heylight_financing_payment_method_if_order_amount_is_equal_or_under_100(
        PaymentInterface $payment,
        PaymentMethodInterface $heylightPaymentMethod,
        PaymentMethodInterface $otherPaymentMethod,
        OrderInterface $order,
    ): void {
        $order->getTotal()->willReturn(10000);
        $this->getSupportedMethods($payment)->shouldReturn([
            0 => $heylightPaymentMethod,
            2 => $otherPaymentMethod,
        ]);

        $order->getTotal()->willReturn(9900);
        $this->getSupportedMethods($payment)->shouldReturn([
            0 => $heylightPaymentMethod,
            2 => $otherPaymentMethod,
        ]);
    }

    public function it_does_not_resolve_heylight_payment_methods_if_order_currency_is_not_supported(
        PaymentInterface $payment,
        PaymentMethodInterface $otherPaymentMethod,
        OrderInterface $order,
    ): void {
        $order->getCurrencyCode()->willReturn('USD');
        $this->getSupportedMethods($payment)->shouldReturn([
            2 => $otherPaymentMethod,
        ]);
    }

    public function it_does_not_resolve_heylight_payment_methods_if_country_code_is_not_supported(
        PaymentInterface $payment,
        PaymentMethodInterface $otherPaymentMethod,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
    ): void {
        $billingAddress->getCountryCode()->willReturn('US');
        $this->getSupportedMethods($payment)->shouldReturn([
            2 => $otherPaymentMethod,
        ]);

        $shippingAddress->getCountryCode()->willReturn('US');
        $this->getSupportedMethods($payment)->shouldReturn([
            2 => $otherPaymentMethod,
        ]);
    }

    public function it_resolves_heylight_payment_methods_if_term_amount_is_equals_or_greater_than_minimum(
        PaymentInterface $payment,
        PaymentMethodInterface $heylightPaymentMethod,
        PaymentMethodInterface $otherPaymentMethod,
        GatewayConfigInterface $heylightBnplGatewayConfig,
        GatewayConfigInterface $heylightFinancingGatewayConfig,
        OrderInterface $order,
    ): void {
        $order->getTotal()->willReturn(6000);
        $heylightBnplGatewayConfig->getConfig()->willReturn(['allowed_terms' => [3,4,5]]);
        $heylightFinancingGatewayConfig->getConfig()->willReturn(['allowed_terms' => [3,4,5]]);

        $this->getSupportedMethods($payment)->shouldReturn([
            0 => $heylightPaymentMethod,
            2 => $otherPaymentMethod,
        ]);
    }

    public function it_does_not_resolve_heylight_payment_methods_if_term_amount_is_less_than_minimum(
        PaymentInterface $payment,
        PaymentMethodInterface $otherPaymentMethod,
        GatewayConfigInterface $heylightBnplGatewayConfig,
        GatewayConfigInterface $heylightFinancingGatewayConfig,
        OrderInterface $order,
    ): void {
        $order->getTotal()->willReturn(5900);
        $heylightBnplGatewayConfig->getConfig()->willReturn(['allowed_terms' => [3,4,5]]);
        $heylightFinancingGatewayConfig->getConfig()->willReturn(['allowed_terms' => [3,4,5]]);

        $this->getSupportedMethods($payment)->shouldReturn([
            2 => $otherPaymentMethod,
        ]);
    }
}
