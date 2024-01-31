<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Client\ValueObject;

use Webgriffe\SyliusPagolightPlugin\Client\Config;
use Webmozart\Assert\Assert;

final class Address
{
    public function __construct(
        private readonly ?string $streetNumber,
        private readonly ?string $street,
        private readonly ?string $addressLine1,
        private readonly ?string $apartment,
        private readonly ?string $addressLine2,
        private readonly string $zipCode,
        private readonly string $city,
        private readonly string $countryCode,
        private readonly ?string $regionCode,
        private readonly ?string $subRegionCode,
    ) {
        Assert::lengthBetween($this->zipCode, 4, 5);
        Assert::oneOf($this->countryCode, Config::ALLOWED_COUNTRY_CODES);
    }

    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getApartment(): ?string
    {
        return $this->apartment;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function getRegionCode(): ?string
    {
        return $this->regionCode;
    }

    public function getSubRegionCode(): ?string
    {
        return $this->subRegionCode;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }
}
