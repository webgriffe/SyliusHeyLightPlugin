<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Client\PaymentState;
use Webgriffe\SyliusPagolightPlugin\PaymentDetailsHelper;
use Webmozart\Assert\Assert;

/**
 * @psalm-type PaymentDetails array{contract_uuid: string, redirect_url: string, created_at: string, status?: string}
 */
final class StatusAction implements ActionInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param GetStatus|mixed $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        Assert::isInstanceOf($request, GetStatus::class);

        /** @var SyliusPaymentInterface $payment */
        $payment = $request->getFirstModel();

        $this->logger->info(sprintf(
            'Start status action for Sylius payment with ID "%s".',
            (string) $payment->getId(),
        ));

        $paymentDetails = $payment->getDetails();

        if ($paymentDetails === []) {
            $this->logger->info('Empty stored details.');
            $request->markNew();

            return;
        }

        if (!$request->isNew() && !$request->isUnknown()) {
            $this->logger->info('Request new or unknown.', ['isNew' => $request->isNew(), 'isUnknown' => $request->isUnknown()]);

            // Payment status already set
            return;
        }

        if (!PaymentDetailsHelper::areValid($paymentDetails)) {
            $this->logger->info('Payment details not valid. Payment marked as failed');
            $request->markFailed();

            return;
        }

        /** @psalm-suppress InvalidArgument */
        $paymentStatus = PaymentDetailsHelper::getPaymentStatus($paymentDetails);

        if (in_array($paymentStatus, [PaymentState::CANCELLED, PaymentState::PENDING], true)) {
            $this->logger->info('Payment cancelled or pending. Payment marked as canceled.');
            $request->markCanceled();

            return;
        }

        if (in_array($paymentStatus, [PaymentState::SUCCESS, PaymentState::AWAITING_CONFIRMATION], true)) {
            $this->logger->info('Payment successfully or awaiting confirmation. Payment marked as captured.');
            $request->markCaptured();

            return;
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
