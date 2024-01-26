<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\PaymentState;
use Webgriffe\SyliusPagolightPlugin\Domain\PaymentDetailsHelper;

final class StatusAction implements ActionInterface
{
    /**
     * @param GetStatus $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var SyliusPaymentInterface $payment */
        $payment = $request->getFirstModel();

        $paymentDetails = $payment->getDetails();

        PaymentDetailsHelper::assertPaymentDetailsAreValid($paymentDetails);

        $paymentStatus = PaymentDetailsHelper::getPaymentStatus($paymentDetails);

        if ($paymentStatus === PaymentState::CANCELLED) {
            $request->markCanceled();

            return;
        }

        if ($paymentStatus === PaymentState::PENDING) {
            $request->markPending();

            return;
        }

        if ($paymentStatus === PaymentState::AWAITING_CONFIRMATION) {
            $request->markPending();

            return;
        }

        if ($paymentStatus === PaymentState::SUCCESS) {
            $request->markCaptured();
        }
    }

    public function supports($request): bool
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getFirstModel() instanceof SyliusPaymentInterface
        ;
    }
}
