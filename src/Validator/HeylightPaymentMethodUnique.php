<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class HeylightPaymentMethodUnique extends Constraint
{
    public string $message = 'webgriffe_sylius_heylight.payment_method.unique';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
