<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Client\ValueObject;

use Webgriffe\SyliusPagolightPlugin\Client\Config;
use Webmozart\Assert\Assert;

final class Contract
{
    /**
     * @param ProductInformation[] $products
     * @param int[]|null $allowedTerms
     * @param array<string, string>|null $additionalData
     */
    public function __construct(
        private readonly Amount $amount,
        private readonly string $amountFormat,
        private readonly RedirectUrls $redirectUrls,
        private readonly CustomerDetails $customerDetails,
        private readonly array $products = [],
        private readonly ?Webhooks $webhooks = null,
        private readonly null|Address|string $billingAddress = null,
        private readonly null|Address|string $shippingAddress = null,
        private readonly ?string $orderReference = null,
        private readonly ?array $allowedTerms = null,
        private readonly ?array $additionalData = null,
        private readonly ?ClientMetadata $clientMetadata = null,
        private readonly ?string $language = null,
        private readonly ?string $storeId = null,
        private readonly ?string $config = null,
        private readonly ?string $merchantUserUuid = null,
        private readonly ?bool $delayFinalisation = null,
    ) {
        Assert::oneOf($this->amountFormat, [Config::MINOR_UNIT, Config::DECIMAL]);
        Assert::oneOf($this->language, Config::ALLOWED_LANGUAGE_CODES);
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getAmountFormat(): string
    {
        return $this->amountFormat;
    }

    public function getRedirectUrls(): RedirectUrls
    {
        return $this->redirectUrls;
    }

    public function getCustomerDetails(): CustomerDetails
    {
        return $this->customerDetails;
    }

    /**
     * @return ProductInformation[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    public function getWebhooks(): ?Webhooks
    {
        return $this->webhooks;
    }

    public function getShippingAddress(): Address|string|null
    {
        return $this->shippingAddress;
    }

    public function getBillingAddress(): string|Address|null
    {
        return $this->billingAddress;
    }

    public function getOrderReference(): ?string
    {
        return $this->orderReference;
    }

    public function getAllowedTerms(): ?array
    {
        return $this->allowedTerms;
    }

    /**
     * @return array<string, string>|null
     */
    public function getAdditionalData(): ?array
    {
        return $this->additionalData;
    }

    public function getClientMetadata(): ?ClientMetadata
    {
        return $this->clientMetadata;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getStoreId(): ?string
    {
        return $this->storeId;
    }

    public function getConfig(): ?string
    {
        return $this->config;
    }

    public function getMerchantUserUuid(): ?string
    {
        return $this->merchantUserUuid;
    }

    public function getDelayFinalisation(): ?bool
    {
        return $this->delayFinalisation;
    }

    /**
     * @return array<string, mixed> The array representation of the contract to send to Pagolight API
     */
    public function toArrayParams(): array
    {
        $contractParams = [
            'amount' => [
                'currency' => $this->amount->getCurrency(),
                'amount' => $this->amount->getAmount(),
            ],
            'amount_format' => $this->getAmountFormat(),
            'redirect_urls' => [
                'success_url' => $this->redirectUrls->getSuccessUrl(),
                'failure_url' => $this->redirectUrls->getFailureUrl(),
                'cancel_url' => $this->redirectUrls->getCancelUrl(),
            ],
            'customer_details' => [
                'email_address' => $this->getCustomerDetails()->getEmailAddress(),
                'title' => $this->getCustomerDetails()->getTitle(),
                'first_name' => $this->getCustomerDetails()->getFirstName(),
                'last_name' => $this->getCustomerDetails()->getLastName(),
                'date_of_birth' => $this->getCustomerDetails()->getDateOfBirth(),
                'contact_number' => $this->getCustomerDetails()->getContactNumber(),
                'company_name' => $this->getCustomerDetails()->getCompanyName(),
                'residence' => $this->getCustomerDetails()->getResidence(),
                'additional_data' => $this->getCustomerDetails()->getAdditionalData(),
            ],
        ];

        foreach ($this->getProducts() as $productInformation) {
            $productParams = [
                'name' => $productInformation->getName(),
                'price' => $productInformation->getPrice(),
                'quantity' => $productInformation->getQuantity(),
            ];
            if ($productInformation->getSku() !== null) {
                $productParams['sku'] = $productInformation->getSku();
            }
            $contractParams['products'][] = $productParams;
        }

        if ($this->getWebhooks() !== null) {
            $contractParams['webhooks'] = [
                'status_url' => $this->getWebhooks()->getStatusUrl(),
                'mapping_scheme' => $this->getWebhooks()->getMappingScheme(),
                'token' => $this->getWebhooks()->getToken(),
            ];
        }

        $billingAddress = $this->getBillingAddress();
        if ($billingAddress instanceof Address) {
            $contractParams['billing_address'] = $this->getAddressParams($billingAddress);
        } elseif (is_string($billingAddress)) {
            $contractParams['billing_address_raw'] = $billingAddress;
        }

        $shippingAddress = $this->getShippingAddress();
        if ($shippingAddress instanceof Address) {
            $contractParams['shipping_address'] = $this->getAddressParams($shippingAddress);
        } elseif (is_string($shippingAddress)) {
            $contractParams['shipping_address_raw'] = $shippingAddress;
        }

        if ($this->getOrderReference() !== null) {
            $contractParams['order_reference'] = $this->getOrderReference();
        }

        if ($this->getAllowedTerms() !== [] && $this->getAllowedTerms() !== null) {
            $contractParams['allowed_terms'] = $this->getAllowedTerms();
        }

        if ($this->getAdditionalData() !== null) {
            $contractParams['additional_data'] = $this->getAdditionalData();
        }

        if ($this->getClientMetadata() !== null) {
            $contractParams['client_metadata'] = [
                'ecommerce_platform' => $this->getClientMetadata()->getEcommercePlatform(),
                'locale' => $this->getClientMetadata()->getLocale(),
            ];
        }

        if ($this->getLanguage() !== null) {
            $contractParams['language'] = $this->getLanguage();
        }

        if ($this->getStoreId() !== null) {
            $contractParams['store_id'] = $this->getStoreId();
        }

        if ($this->getConfig() !== null) {
            $contractParams['config'] = $this->getConfig();
        }

        if ($this->getMerchantUserUuid() !== null) {
            $contractParams['merchant_user_uuid'] = $this->getMerchantUserUuid();
        }

        if ($this->getDelayFinalisation() !== null) {
            $contractParams['delay_finalisation'] = $this->getDelayFinalisation();
        }

        return $contractParams;
    }

    private function getAddressParams(Address $address): array
    {
        $addressPayload = [
            'zip_code' => $address->getZipCode(),
            'city' => $address->getCity(),
            'country_code' => $address->getCountryCode(),
        ];
        if ($address->getStreetNumber() !== null) {
            $addressPayload['street_number'] = $address->getStreetNumber();
        }
        if ($address->getStreet() !== null) {
            $addressPayload['street'] = $address->getStreet();
        }
        if ($address->getAddressLine1() !== null) {
            $addressPayload['address_line_1'] = $address->getAddressLine1();
        }
        if ($address->getApartment() !== null) {
            $addressPayload['apartment'] = $address->getApartment();
        }
        if ($address->getAddressLine2() !== null) {
            $addressPayload['address_line_2'] = $address->getAddressLine2();
        }
        if ($address->getRegionCode() !== null) {
            $addressPayload['region_code'] = $address->getRegionCode();
        }
        if ($address->getSubRegionCode() !== null) {
            $addressPayload['sub_region_code'] = $address->getSubRegionCode();
        }

        return $addressPayload;
    }
}
