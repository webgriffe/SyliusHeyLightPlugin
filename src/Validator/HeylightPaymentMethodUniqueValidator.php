<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Validator;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Webgriffe\SyliusHeylightPlugin\Payum\HeylightApi;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class HeylightPaymentMethodUniqueValidator extends ConstraintValidator
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

        if (!$constraint instanceof HeylightPaymentMethodUnique) {
            throw new UnexpectedValueException($constraint, HeylightPaymentMethodUnique::class);
        }

        $gatewayConfig = $value->getGatewayConfig();
        /** @psalm-suppress DeprecatedMethod */
        if ($gatewayConfig === null ||
            !in_array($gatewayConfig->getFactoryName(), [
                HeylightApi::HEYLIGHT_BNPL_GATEWAY_CODE,
                HeylightApi::HEYLIGHT_FINANCING_GATEWAY_CODE,
            ], true)
        ) {
            return;
        }

        /** @var PaymentMethodInterface[] $paymentMethods */
        $paymentMethods = $this->paymentMethodRepository->findAll();
        /** @psalm-suppress DeprecatedMethod */
        $paymentMethodsWithSameGatewayConfig = array_filter(
            $paymentMethods,
            static fn (PaymentMethodInterface $paymentMethod) => $paymentMethod->getGatewayConfig()?->getFactoryName() === $gatewayConfig->getFactoryName(),
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
