<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class PagolightPaymentMethodUnique extends Constraint
{
    public string $message = 'webgriffe_sylius_pagolight.payment_method.unique';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
