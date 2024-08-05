<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin;

use Webgriffe\SyliusPagolightPlugin\Client\PaymentState;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Response\ContractCreateResult;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

/**
 * @psalm-type PaymentDetails array{contract_uuid: string, redirect_url: string, created_at: string, status?: string}
 */
final class PaymentDetailsHelper
{
    private const CONTRACT_UUID_KEY = 'contract_uuid';

    private const REDIRECT_URL_KEY = 'redirect_url';

    private const CREATED_AT_KEY = 'created_at';

    private const STATUS_KEY = 'status';

    /**
     * @return PaymentDetails
     */
    public static function createFromContractCreateResult(ContractCreateResult $contractCreateResult): array
    {
        return [
            self::CONTRACT_UUID_KEY => $contractCreateResult->getUuid(),
            self::REDIRECT_URL_KEY => $contractCreateResult->getRedirectUrl(),
            self::CREATED_AT_KEY => $contractCreateResult->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @phpstan-assert PaymentDetails $paymentDetails
     *
     * @throws InvalidArgumentException
     */
    public static function assertPaymentDetailsAreValid(array $paymentDetails): void
    {
        Assert::keyExists($paymentDetails, self::CONTRACT_UUID_KEY);
        Assert::stringNotEmpty($paymentDetails[self::CONTRACT_UUID_KEY]);

        Assert::keyExists($paymentDetails, self::REDIRECT_URL_KEY);
        Assert::stringNotEmpty($paymentDetails[self::REDIRECT_URL_KEY]);

        Assert::keyExists($paymentDetails, self::CREATED_AT_KEY);
        Assert::stringNotEmpty($paymentDetails[self::CREATED_AT_KEY]);

        if (array_key_exists(self::STATUS_KEY, $paymentDetails)) {
            Assert::stringNotEmpty($paymentDetails[self::STATUS_KEY]);
            Assert::oneOf($paymentDetails[self::STATUS_KEY], PaymentState::cases());
        }
    }

    /**
     * @param PaymentDetails $paymentDetails
     */
    public static function getContractUuid(array $paymentDetails): string
    {
        return $paymentDetails[self::CONTRACT_UUID_KEY];
    }

    /**
     * @param PaymentDetails|array $paymentDetails
     */
    public static function addPaymentStatus(array $paymentDetails, string $status): array
    {
        return array_merge($paymentDetails, [self::STATUS_KEY => $status]);
    }

    /**
     * @param PaymentDetails $paymentDetails
     */
    public static function getPaymentStatus(array $paymentDetails): ?string
    {
        return $paymentDetails[self::STATUS_KEY] ?? null;
    }

    /**
     * @param PaymentDetails $paymentDetails
     */
    public static function getRedirectUrl(array $paymentDetails): string
    {
        return $paymentDetails[self::REDIRECT_URL_KEY];
    }

    /**
     * @phpstan-assert-if-true PaymentDetails $storedPaymentDetails
     */
    public static function areValid(array $storedPaymentDetails): bool
    {
        try {
            self::assertPaymentDetailsAreValid($storedPaymentDetails);
        } catch (InvalidArgumentException) {
            return false;
        }

        return true;
    }
}
