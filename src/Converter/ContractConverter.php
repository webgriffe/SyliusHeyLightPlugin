<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Converter;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webgriffe\SyliusHeylightPlugin\Client\Config;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\Address;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\Amount;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\ClientMetadata;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\Contract;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\CustomerDetails;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\ProductInformation;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\RedirectUrls;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\Webhooks;
use Webmozart\Assert\Assert;

final class ContractConverter implements ContractConverterInterface
{
    #[\Override]
    public function convertFromPayment(
        PaymentInterface $payment,
        string $successUrl,
        string $failureUrl,
        ?string $cancelUrl = null,
        ?string $webhookUrl = null,
        ?string $webhookToken = null,
        array $allowedTerms = [],
        array $additionalData = [],
    ): Contract {
        $currency = $payment->getCurrencyCode();
        Assert::notNull($currency);

        $order = $payment->getOrder();
        Assert::isInstanceOf($order, OrderInterface::class);

        $orderBillingAddress = $order->getBillingAddress();
        $orderShippingAddress = $order->getShippingAddress();

        $paymentMethod = $payment->getMethod();
        Assert::isInstanceOf($paymentMethod, PaymentMethodInterface::class);

        $customer = $order->getCustomer();
        Assert::isInstanceOf($customer, CustomerInterface::class);

        $emailAddress = $customer->getEmail();
        Assert::stringNotEmpty($emailAddress, 'Email is required to create a contract on Heylight');

        $webhooks = null;
        if ($webhookUrl !== null && $webhookToken !== null) {
            $webhooks = new Webhooks($webhookUrl, $webhookToken);
        }

        $localeCode = $order->getLocaleCode();
        Assert::string($localeCode);

        $products = [];
        foreach ($order->getItems() as $orderItem) {
            $product = $orderItem->getProduct();
            Assert::isInstanceOf($product, ProductInterface::class);
            $variant = $orderItem->getVariant();
            Assert::isInstanceOf($variant, ProductVariantInterface::class);

            $productVariantType = null;
            if ($product->isConfigurable()) {
                $firstOptionValue = $variant->getOptionValues()->first();
                if ($firstOptionValue !== false) {
                    $productVariantType = $firstOptionValue->getOption()?->getTranslation($localeCode)->getName();
                }
            }

            $products[] = new ProductInformation(
                (string) $product->getId(),
                (string) $product->getCode(),
                (string) $orderItem->getProductName(),
                $orderItem->getQuantity(),
                (string) $orderItem->getUnitPrice(),
                (string) $product->getShortDescription(),
                $productVariantType,
                $variant->getCode(),
                null,
                null,
                null,
            );
        }

        $billingAddress = null;
        if ($orderBillingAddress instanceof AddressInterface) {
            $countryCode = $orderBillingAddress->getCountryCode();
            $provinceCode = $orderBillingAddress->getProvinceCode();
            $regionCode = null;
            if ($countryCode !== null && $provinceCode !== null && !str_contains($provinceCode, '-')) {
                $regionCode = $countryCode . '-' . $provinceCode;
            } elseif ($provinceCode !== null && str_contains($provinceCode, '-')) {
                $regionCode = $provinceCode;
            }

            $billingAddress = new Address(
                null,
                null,
                (string) $orderBillingAddress->getStreet(),
                null,
                null,
                (string) $orderBillingAddress->getPostcode(),
                (string) $orderBillingAddress->getCity(),
                (string) $countryCode,
                $regionCode,
                $provinceCode,
            );
        }

        $shippingAddress = null;
        if ($orderShippingAddress instanceof AddressInterface) {
            $countryCode = $orderShippingAddress->getCountryCode();
            $provinceCode = $orderShippingAddress->getProvinceCode();
            $regionCode = null;
            if ($countryCode !== null && $provinceCode !== null && !str_contains($provinceCode, '-')) {
                $regionCode = $countryCode . '-' . $provinceCode;
            } elseif ($provinceCode !== null && str_contains($provinceCode, '-')) {
                $regionCode = $provinceCode;
            }

            $shippingAddress = new Address(
                null,
                null,
                (string) $orderShippingAddress->getStreet(),
                null,
                null,
                (string) $orderShippingAddress->getPostcode(),
                (string) $orderShippingAddress->getCity(),
                (string) $orderShippingAddress->getCountryCode(),
                $regionCode,
                $orderShippingAddress->getProvinceCode(),
            );
        }

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
                $orderBillingAddress?->getCompany(),
                $orderBillingAddress?->getStreet(),
                '',
            ),
            $products,
            $webhooks,
            $billingAddress,
            $shippingAddress,
            $order->getNumber(),
            $allowedTerms,
            $additionalData,
            new ClientMetadata($localeCode),
            $this->getSupportedLanguageFromLocaleCode($localeCode),
            $order->getChannel()?->getCode(),
        );
    }

    private function getSupportedLanguageFromLocaleCode(string $localeCode): string
    {
        if ($localeCode === 'en_GB') {
            return Config::EN_GB_LANGUAGE_CODE;
        }
        if (str_starts_with($localeCode, 'en')) {
            return Config::EN_LANGUAGE_CODE;
        }
        if (str_starts_with($localeCode, 'it')) {
            return Config::IT_LANGUAGE_CODE;
        }
        if (str_starts_with($localeCode, 'fr')) {
            return Config::FR_LANGUAGE_CODE;
        }
        if (str_starts_with($localeCode, 'de')) {
            return Config::DE_LANGUAGE_CODE;
        }

        return Config::EN_LANGUAGE_CODE;
    }
}
