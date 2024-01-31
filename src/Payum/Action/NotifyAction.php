<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Webgriffe\SyliusPagolightPlugin\PaymentDetailsHelper;
use Webmozart\Assert\Assert;

/**
 * @psalm-type PaymentDetails array{contract_uuid: string, redirect_url: string, created_at: string, status?: string}
 */
final class NotifyAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @param Notify|mixed $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        Assert::isInstanceOf($request, Notify::class);

        /** @var SyliusPaymentInterface|mixed $payment */
        $payment = $request->getModel();
        Assert::isInstanceOf($payment, SyliusPaymentInterface::class);

        // This is needed to populate the http request with GET and POST params from current request
        $this->gateway->execute($httpRequest = new GetHttpRequest());

        /** @var array<string, string> $requestParameters */
        $requestParameters = $httpRequest->request;

        /** @var PaymentDetails $paymentDetails */
        $paymentDetails = $payment->getDetails();
        PaymentDetailsHelper::assertPaymentDetailsAreValid($paymentDetails);

        $payment->setDetails(PaymentDetailsHelper::addPaymentStatus(
            $paymentDetails,
            $requestParameters['status'],
        ));
    }

    public function supports($request): bool
    {
        return $request instanceof Notify &&
            $request->getModel() instanceof SyliusPaymentInterface
        ;
    }
}
