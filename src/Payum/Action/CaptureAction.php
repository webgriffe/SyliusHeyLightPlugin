<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Payum\Core\Security\TokenInterface;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Webgriffe\SyliusPagolightPlugin\Client\Exception\ClientException;
use Webgriffe\SyliusPagolightPlugin\Client\PaymentState;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Contract;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Response\ContractCreateResult;
use Webgriffe\SyliusPagolightPlugin\Controller\PaymentController;
use Webgriffe\SyliusPagolightPlugin\Generator\WebhookTokenGeneratorInterface;
use Webgriffe\SyliusPagolightPlugin\PaymentDetailsHelper;
use Webgriffe\SyliusPagolightPlugin\Payum\PagolightApi;
use Webgriffe\SyliusPagolightPlugin\Payum\Request\Api\CreateContract;
use Webgriffe\SyliusPagolightPlugin\Payum\Request\ConvertPaymentToContract;
use Webgriffe\SyliusPagolightPlugin\Repository\WebhookTokenRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @psalm-type PaymentDetails array{contract_uuid: string, redirect_url: string, created_at: string, status?: string}
 *
 * @psalm-suppress PropertyNotSetInConstructor Api and gateway are injected via container configuration
 */
final class CaptureAction implements ActionInterface, GatewayAwareInterface, GenericTokenFactoryAwareInterface, ApiAwareInterface
{
    use GatewayAwareTrait, GenericTokenFactoryAwareTrait, ApiAwareTrait;

    public function __construct(
        private readonly RouterInterface $router,
        private readonly WebhookTokenGeneratorInterface $webhookTokenGenerator,
        private readonly LoggerInterface $logger,
        private readonly RequestStack $requestStack,
        private readonly WebhookTokenRepositoryInterface $webhookTokenRepository,
    ) {
        $this->apiClass = PagolightApi::class;
    }

    /**
     * @param Capture|mixed $request
     *
     * @throws ClientException
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        Assert::isInstanceOf($request, Capture::class);

        /** @var SyliusPaymentInterface $payment */
        $payment = $request->getModel();

        /** @var string|int $paymentId */
        $paymentId = $payment->getId();
        $this->logger->info(sprintf(
            'Start capture action for Sylius payment with ID "%s".',
            $paymentId,
        ));

        $captureToken = $request->getToken();
        Assert::isInstanceOf($captureToken, TokenInterface::class);

        $paymentDetails = $payment->getDetails();

        if ($paymentDetails !== []) {
            if (!PaymentDetailsHelper::areValid($paymentDetails)) {
                $this->logger->error('Payment details are already populated with others data. Maybe this payment should be marked as error');
                $payment->setDetails(PaymentDetailsHelper::addPaymentStatus(
                    $paymentDetails,
                    PaymentState::CANCELLED,
                ));

                return;
            }
            $this->logger->info(
                'Pagolight payment details are already valued, so no need to continue here. Redirecting the user to the Sylius Pagolight Payments waiting page.',
            );

            $session = $this->requestStack->getSession();
            $session->set(PaymentController::PAYMENT_ID_SESSION_KEY, $paymentId);
            $session->set(PaymentController::TOKEN_HASH_SESSION_KEY, $captureToken->getHash());

            $order = $payment->getOrder();
            Assert::isInstanceOf($order, OrderInterface::class);

            throw new HttpRedirect(
                $this->router->generate('webgriffe_sylius_pagolight_plugin_payment_process', [
                    'tokenValue' => $order->getTokenValue(),
                    '_locale' => $order->getLocaleCode(),
                ]),
            );
        }

        $captureUrl = $captureToken->getTargetUrl();

        $cancelToken = $this->tokenFactory->createToken($captureToken->getGatewayName(), $captureToken->getDetails(), 'payum_cancel_do', [], $captureToken->getAfterUrl());
        $cancelUrl = $cancelToken->getTargetUrl();

        $notifyToken = $this->tokenFactory->createNotifyToken($captureToken->getGatewayName(), $captureToken->getDetails());
        $notifyUrl = $notifyToken->getTargetUrl();

        $additionalData = [];
        $paymentMethod = $payment->getMethod();
        Assert::isInstanceOf($paymentMethod, PaymentMethodInterface::class);
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        /** @psalm-suppress DeprecatedMethod */
        if ($gatewayConfig instanceof GatewayConfigInterface &&
            $gatewayConfig->getFactoryName() === PagolightApi::PAGOLIGHT_PRO_GATEWAY_CODE
        ) {
            $additionalData['pricing_structure_code'] = 'PC7';
        }

        $pagolightApi = $this->api;
        Assert::isInstanceOf($pagolightApi, PagolightApi::class);

        $webhookToken = $this->getWebhookToken($payment);
        $convertPaymentToContract = new ConvertPaymentToContract(
            $payment,
            $captureUrl,
            $cancelUrl,
            $cancelUrl,
            $notifyUrl,
            $webhookToken,
            $pagolightApi->getAllowedTerms(),
            $additionalData,
        );

        try {
            $this->gateway->execute($convertPaymentToContract);
        } catch (\Throwable $e) {
            $this->logger->error(sprintf(
                'An error occurred while converting the payment to a contract: %s',
                $e->getMessage(),
            ), $e->getTrace());
            $payment->setDetails(PaymentDetailsHelper::addPaymentStatus(
                $paymentDetails,
                PaymentState::CANCELLED,
            ));

            return;
        }
        $contract = $convertPaymentToContract->getContract();
        Assert::isInstanceOf($contract, Contract::class);

        $createContract = new CreateContract($contract);
        $this->gateway->execute($createContract);
        $contractCreateResult = $createContract->getResult();
        Assert::isInstanceOf($contractCreateResult, ContractCreateResult::class);

        $payment->setDetails(
            PaymentDetailsHelper::createFromContractCreateResult($contractCreateResult),
        );

        $this->logger->info(sprintf(
            'Redirecting the user to the Pagolight redirect URL "%s".',
            $contractCreateResult->getRedirectUrl(),
        ));

        throw new HttpRedirect($contractCreateResult->getRedirectUrl());
    }

    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof SyliusPaymentInterface
        ;
    }

    private function getWebhookToken(SyliusPaymentInterface $payment): string
    {
        $webhookToken = $this->webhookTokenRepository->findOneByPayment($payment);
        if ($webhookToken !== null) {
            return $webhookToken->getToken();
        }

        return $this->webhookTokenGenerator->generateForPayment($payment)->getToken();
    }
}
