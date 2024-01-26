<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum;

final class PagolightApi
{
    private const API_URL_TEST = 'https://sbx-origination.heidipay.io';

    private const API_URL_LIVE = 'https://origination.heidipay.com';

    /**
     * @param array{sandbox: bool, merchant_key: string} $config
     */
    public function __construct(private readonly array $config)
    {
    }

    public function getApiEndpoint(): string
    {
        return $this->config['sandbox'] ? self::API_URL_TEST : self::API_URL_LIVE;
    }

    public function getMerchantKey(): string
    {
        return $this->config['merchant_key'];
    }

    public function isSandBox(): bool
    {
        return $this->config['sandbox'];
    }
}
