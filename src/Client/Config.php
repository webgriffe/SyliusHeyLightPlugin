<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Client;

final class Config
{
    public const SANDBOX_BASE_URL = 'https://sbx-origination.heidipay.io';

    public const PRODUCTION_BASE_URL = 'https://origination.heidipay.com';

    public const MINOR_UNIT = 'MINOR_UNIT';

    public const DECIMAL = 'DECIMAL';

    public const CHF_CURRENCY_CODE = 'CHF';

    public const EUR_CURRENCY_CODE = 'EUR';

    public const GBP_CURRENCY_CODE = 'GBP';

    public const ALLOWED_CURRENCY_CODES = [
        self::CHF_CURRENCY_CODE,
        self::EUR_CURRENCY_CODE,
        self::GBP_CURRENCY_CODE,
    ];

    public const CH_COUNTRY_CODE = 'CH';

    public const IT_COUNTRY_CODE = 'IT';

    public const GB_COUNTRY_CODE = 'GB';

    public const ALLOWED_COUNTRY_CODES = [
        self::CH_COUNTRY_CODE,
        self::IT_COUNTRY_CODE,
        self::GB_COUNTRY_CODE,
    ];

    public const EN_LANGUAGE_CODE = 'en';

    public const FR_LANGUAGE_CODE = 'fr';

    public const DE_LANGUAGE_CODE = 'de';

    public const EN_GB_LANGUAGE_CODE = 'en-gb';

    public const IT_LANGUAGE_CODE = 'it';

    public const ALLOWED_LANGUAGE_CODES = [
        self::EN_LANGUAGE_CODE,
        self::FR_LANGUAGE_CODE,
        self::DE_LANGUAGE_CODE,
        self::EN_GB_LANGUAGE_CODE,
        self::IT_LANGUAGE_CODE,
    ];
}
