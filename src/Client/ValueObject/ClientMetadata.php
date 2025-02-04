<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Client\ValueObject;

final class ClientMetadata
{
    public function __construct(
        private string $locale,
        private string $ecommercePlatform = 'Sylius',
    ) {
    }

    public function getEcommercePlatform(): string
    {
        return $this->ecommercePlatform;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
