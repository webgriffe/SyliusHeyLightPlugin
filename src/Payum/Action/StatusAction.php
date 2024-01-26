<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Client\PaymentState;
use Webgriffe\SyliusPagolightPlugin\PaymentDetailsHelper;
use Webmozart\Assert\Assert;

/**
 * @psalm-type PaymentDetails array{contract_uuid: string, redirect_url: string, created_at: string, expire_at: string, status?: string}
 */
final class StatusAction implements ActionInterface
{
    /**
     * @param GetStatus|mixed $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        Assert::isInstanceOf($request, GetStatus::class);

        /** @var SyliusPaymentInterface $payment */
        $payment = $request->getFirstModel();

        /** @var PaymentDetails|array{} $paymentDetails */
        $paymentDetails = $payment->getDetails();

        if ($paymentDetails === []) {
            $request->markNew();

            return;
        }

        PaymentDetailsHelper::assertPaymentDetailsAreValid($paymentDetails);

        /** @psalm-suppress InvalidArgument */
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
            $request instanceof GetStatus &&
            $request->getFirstModel() instanceof SyliusPaymentInterface
        ;
    }
}
