<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusPagolightPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Webmozart\Assert\Assert;

final class PaymentContext implements Context
{
    public const MERCHANT_KEY = '83Y4TDI8W7Y4EWIY48TWT';

    public function __construct(
        private readonly SharedStorageInterface $sharedStorage,
        private readonly PaymentMethodRepositoryInterface $paymentMethodRepository,
        private readonly ExampleFactoryInterface $paymentMethodExampleFactory,
        private readonly ObjectManager $paymentMethodManager,
        private readonly array $gatewayFactories,
    ) {
    }

    /**
     * @Given the store has (also) a payment method :paymentMethodName with a code :paymentMethodCode and Pagolight Payment Checkout gateway
     */
    public function theStoreHasPaymentMethodWithCodeAndPaypalExpressCheckoutGateway(
        string $paymentMethodName,
        string $paymentMethodCode,
    ): void {
        $paymentMethod = $this->createPaymentMethod($paymentMethodName, $paymentMethodCode, $paymentMethodName);
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::isInstanceOf($gatewayConfig, GatewayConfigInterface::class);

        $gatewayConfig->setConfig([
            'sandbox' => false,
            'merchant_key' => self::MERCHANT_KEY,
            'allowed_terms' => [3, 6, 12, 24],
        ]);

        $this->paymentMethodManager->flush();
    }

    private function createPaymentMethod(
        string $name,
        string $code,
        string $gatewayFactory = 'Pagolight',
        string $description = '',
        bool $addForCurrentChannel = true,
        ?int $position = null,
    ): PaymentMethodInterface {
        $gatewayFactory = array_search($gatewayFactory, $this->gatewayFactories, true);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodExampleFactory->create([
            'name' => ucfirst($name),
            'code' => $code,
            'description' => $description,
            'gatewayName' => $gatewayFactory,
            'gatewayFactory' => $gatewayFactory,
            'enabled' => true,
            'channels' => ($addForCurrentChannel && $this->sharedStorage->has('channel')) ? [$this->sharedStorage->get('channel')] : [],
        ]);

        if (null !== $position) {
            $paymentMethod->setPosition($position);
        }

        $this->sharedStorage->set('payment_method', $paymentMethod);
        $this->paymentMethodRepository->add($paymentMethod);

        return $paymentMethod;
    }
}
