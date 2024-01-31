<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Cancel;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Client\PaymentState;
use Webgriffe\SyliusPagolightPlugin\PaymentDetailsHelper;
use Webmozart\Assert\Assert;

/**
 * @psalm-type PaymentDetails array{contract_uuid: string, redirect_url: string, created_at: string, status?: string}
 */
final class CancelAction implements ActionInterface
{
    /**
     * @param Cancel|mixed $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        Assert::isInstanceOf($request, Cancel::class);

        $payment = $request->getModel();
        Assert::isInstanceOf($payment, SyliusPaymentInterface::class);

        /** @var PaymentDetails $paymentDetails */
        $paymentDetails = $payment->getDetails();
        PaymentDetailsHelper::assertPaymentDetailsAreValid($paymentDetails);

        $paymentDetails = PaymentDetailsHelper::addPaymentStatus(
            $paymentDetails,
            PaymentState::CANCELLED,
        );
        $payment->setDetails($paymentDetails);
    }

    public function supports($request): bool
    {
        return $request instanceof Cancel &&
            $request->getModel() instanceof SyliusPaymentInterface
        ;
    }
}
