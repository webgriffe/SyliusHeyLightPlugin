<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Domain;

use InvalidArgumentException;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\ContractCreateResult;
use Webmozart\Assert\Assert;

/**
 * @psalm-type PaymentDetails array{contract_uuid: string, redirect_url: string, created_at: string, expire_at: string}
 */
final class PaymentDetailsHelper
{
    /**
     * @return PaymentDetails
     */
    public static function createFromContractCreateResult(ContractCreateResult $contractCreateResult): array
    {
        return [
            'contract_uuid' => $contractCreateResult->getUuid(),
            'redirect_url' => $contractCreateResult->getRedirectUrl(),
            'created_at' => $contractCreateResult->getCreatedAt()->format('Y-m-d H:i:s'),
            'expire_at' => $contractCreateResult->getExpireAt()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function assertPaymentDetailsAreValid(array $paymentDetails): void
    {
        Assert::isArray($paymentDetails);
    }

    /**
     * @param PaymentDetails $paymentDetails
     */
    public static function getContractUuid(array $paymentDetails): string
    {
        return $paymentDetails['contract_uuid'];
    }

    public static function addPaymentStatus(array $paymentDetails, $status): array
    {
        return array_merge($paymentDetails, ['status' => $status]);
    }
}
