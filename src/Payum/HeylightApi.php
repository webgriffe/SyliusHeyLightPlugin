<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Payum;

final class HeylightApi
{
    public const HEYLIGHT_BNPL_GATEWAY_CODE = 'heylight_bnpl';

    public const HEYLIGHT_FINANCING_GATEWAY_CODE = 'heylight_financing';

    /**
     * @param array{sandbox: bool, merchant_key: string, allowed_terms: array<array-key, int>} $config
     */
    public function __construct(private readonly array $config)
    {
    }

    public function getMerchantKey(): string
    {
        return $this->config['merchant_key'];
    }

    public function isSandBox(): bool
    {
        return $this->config['sandbox'];
    }

    /**
     * @return array<array-key, int>
     */
    public function getAllowedTerms(): array
    {
        return array_values($this->config['allowed_terms']);
    }
}
