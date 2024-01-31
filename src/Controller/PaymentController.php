<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Controller;

use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Webgriffe\SyliusPagolightPlugin\PaymentDetailsHelper;
use Webmozart\Assert\Assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @psalm-type PaymentDetails array{contract_uuid: string, redirect_url: string, created_at: string, status?: string}
 */
final class PaymentController extends AbstractController
{
    public function __construct(
        private readonly PaymentRepositoryInterface $paymentRepository,
    ) {
    }

    public function statusAction(mixed $paymentId): Response
    {
        /** @var PaymentInterface|mixed $payment */
        $payment = $this->paymentRepository->find($paymentId);
        Assert::nullOrIsInstanceOf($payment, PaymentInterface::class);
        if ($payment === null) {
            throw $this->createNotFoundException();
        }
        $paymentMethod = $payment->getMethod();
        if (!$paymentMethod instanceof PaymentMethodInterface) {
            throw $this->createAccessDeniedException();
        }
        $paymentGatewayConfig = $paymentMethod->getGatewayConfig();
        if (!$paymentGatewayConfig instanceof GatewayConfigInterface) {
            throw $this->createAccessDeniedException();
        }
        if ($paymentGatewayConfig->getGatewayName() !== 'pagolight') {
            throw $this->createAccessDeniedException();
        }

        /** @var PaymentDetails $paymentDetails */
        $paymentDetails = $payment->getDetails();
        PaymentDetailsHelper::assertPaymentDetailsAreValid($paymentDetails);

        $paymentStatus = PaymentDetailsHelper::getPaymentStatus($paymentDetails);

        return $this->json(['captured' => $paymentStatus !== null]);
    }
}
