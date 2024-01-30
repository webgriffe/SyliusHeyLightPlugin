<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Client;

/**
 * @TODO Convert to Enum when PHP 8.1 will be dropped
 */
final class PaymentState
{
    public const SUCCESS = 'success';

    public const PENDING = 'pending'; // Rimane pending per 4/12 ore fino a quando arriva il pending

    public const AWAITING_CONFIRMATION = 'awaiting_confirmation'; // Non ce lo abbiamo, ma è confermato

    public const CANCELLED = 'cancelled';

    public static function cases(): array
    {
        return [
            self::SUCCESS,
            self::PENDING,
            self::AWAITING_CONFIRMATION,
            self::CANCELLED,
        ];
    }
}
