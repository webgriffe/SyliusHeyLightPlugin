<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject;

use DateTimeInterface;

final class CustomerDetails
{
    public function __construct(
        private readonly string $emailAddress,
        private readonly ?string $title,
        private readonly ?string $firstName,
        private readonly ?string $lastName,
        private readonly ?DateTimeInterface $dateOfBirth,
        private readonly ?string $contactNumber,
        private readonly ?string $companyName,
        private readonly ?string $residence,
        private readonly string $additionalData,
    ) {
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getDateOfBirth(): ?DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function getContactNumber(): ?string
    {
        return $this->contactNumber;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getResidence(): ?string
    {
        return $this->residence;
    }

    public function getAdditionalData(): string
    {
        return $this->additionalData;
    }
}
