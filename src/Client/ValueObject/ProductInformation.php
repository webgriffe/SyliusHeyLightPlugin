<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Client\ValueObject;

use Webmozart\Assert\Assert;

final class ProductInformation
{
    /**
     * @param int[] $allowedTerms
     */
    public function __construct(
        private readonly ?string $externalId,
        private readonly ?string $sku,
        private readonly ?string $name,
        private readonly int $quantity,
        private readonly string $price,
        private readonly string $description,
        private readonly ?string $productVariantType = null,
        private readonly ?string $productVariantKey = null,
        private readonly ?string $imageThumbnail = null,
        private readonly ?string $imageOriginal = null,
        private readonly ?string $additionalData = null,
        private readonly ?array $allowedTerms = null,
    ) {
        Assert::greaterThanEq($this->quantity, 1);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getProductVariantType(): ?string
    {
        return $this->productVariantType;
    }

    public function getProductVariantKey(): ?string
    {
        return $this->productVariantKey;
    }

    public function getImageThumbnail(): ?string
    {
        return $this->imageThumbnail;
    }

    public function getImageOriginal(): ?string
    {
        return $this->imageOriginal;
    }

    public function getAdditionalData(): ?string
    {
        return $this->additionalData;
    }

    public function getAllowedTerms(): ?array
    {
        return $this->allowedTerms;
    }
}
