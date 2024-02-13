<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Webgriffe\SyliusPagolightPlugin\PaymentDetailsHelper;
use Webgriffe\SyliusPagolightPlugin\Payum\Request\RemovePaymentWebhookToken;
use Webgriffe\SyliusPagolightPlugin\Payum\Request\RetrievePaymentWebhookToken;
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

        /** @var array{token: string, status: string} $requestParameters */
        $requestParameters = $httpRequest->request;

        $this->gateway->execute($retrievePaymentWebhookToken = new RetrievePaymentWebhookToken($payment));

        $webhookToken = $retrievePaymentWebhookToken->getWebhookToken();
        if ($webhookToken === null ||
            $webhookToken->getToken() !== $requestParameters['token']
        ) {
            // Throw a 404 to avoid leaking information about the existence of the payment or the correctness of the url
            throw new HttpResponse('Not found', 404);
        }

        /** @var PaymentDetails $paymentDetails */
        $paymentDetails = $payment->getDetails();
        PaymentDetailsHelper::assertPaymentDetailsAreValid($paymentDetails);

        $payment->setDetails(PaymentDetailsHelper::addPaymentStatus(
            $paymentDetails,
            $requestParameters['status'],
        ));

        $this->gateway->execute(new RemovePaymentWebhookToken($webhookToken));
    }

    public function supports($request): bool
    {
        return $request instanceof Notify &&
            $request->getModel() instanceof SyliusPaymentInterface
        ;
    }
}
