<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Validator;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Webgriffe\SyliusPagolightPlugin\Payum\PagolightApi;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class PagolightPaymentMethodUniqueValidator extends ConstraintValidator
{
    public function __construct(
        private readonly PaymentMethodRepositoryInterface $paymentMethodRepository,
    ) {
    }

    /**
     * @param mixed|PaymentMethodInterface $value
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof PaymentMethodInterface) {
            throw new UnexpectedValueException($value, PaymentMethodInterface::class);
        }

        if (!$constraint instanceof PagolightPaymentMethodUnique) {
            throw new UnexpectedValueException($constraint, PagolightPaymentMethodUnique::class);
        }

        $gatewayConfig = $value->getGatewayConfig();
        if ($gatewayConfig === null ||
            !in_array($gatewayConfig->getFactoryName(), [
                PagolightApi::PAGOLIGHT_GATEWAY_CODE,
                PagolightApi::PAGOLIGHT_PRO_GATEWAY_CODE,
            ], true)
        ) {
            return;
        }

        $paymentMethods = $this->paymentMethodRepository->findAll();
        $paymentMethodsWithSameGatewayConfig = array_filter(
            $paymentMethods,
            static fn (PaymentMethodInterface $paymentMethod) => $paymentMethod->getGatewayConfig()?->getFactoryName() === $gatewayConfig->getFactoryName()
        );
        if (count($paymentMethodsWithSameGatewayConfig) > 1 ||
            (count($paymentMethodsWithSameGatewayConfig) === 1 && reset($paymentMethodsWithSameGatewayConfig) !== $value)
        ) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('gatewayConfig')
                ->addViolation();
        }
    }
}
