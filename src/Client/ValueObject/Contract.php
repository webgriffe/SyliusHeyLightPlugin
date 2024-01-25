<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Client\ValueObject;

use Webgriffe\SyliusPagolightPlugin\Client\Config;
use Webmozart\Assert\Assert;

final class Contract
{
    public function __construct(
        private readonly Amount $amount,
        private readonly string $amountFormat,
        private readonly RedirectUrls $redirectUrls,
        private readonly CustomerDetails $customerDetails,
    ) {
        Assert::oneOf($this->amountFormat, [Config::MINOR_UNIT, Config::DECIMAL]);
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
     * @return array<string, mixed> The array representation of the contract to send to Pagolight API
     */
    public function toArrayParams(): array
    {
        return [
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
    }
}
