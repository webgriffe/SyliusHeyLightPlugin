<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Webgriffe\SyliusPagolightPlugin\Client\Exception\ClientException;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Contract;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Response\ContractCreateResult;
use Webgriffe\SyliusPagolightPlugin\PaymentDetailsHelper;
use Webgriffe\SyliusPagolightPlugin\Payum\Request\Api\CreateContract;
use Webgriffe\SyliusPagolightPlugin\Payum\Request\ConvertPaymentToContract;
use Webmozart\Assert\Assert;

/**
 * @psalm-type PaymentDetails array{contract_uuid: string, redirect_url: string, created_at: string, status?: string}
 *
 * @psalm-suppress PropertyNotSetInConstructor Api and gateway are injected via container configuration
 */
final class CaptureAction implements ActionInterface, GatewayAwareInterface, GenericTokenFactoryAwareInterface
{
    use GatewayAwareTrait, GenericTokenFactoryAwareTrait;

    public function __construct(
        private readonly Environment $twig,
        private readonly RouterInterface $router,
    ) {
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

        $captureToken = $request->getToken();
        Assert::isInstanceOf($captureToken, TokenInterface::class);

        /** @var PaymentDetails|array{} $paymentDetails */
        $paymentDetails = $payment->getDetails();

        if ($paymentDetails !== []) {
            $paymentStatusUrl = $this->router->generate(
                'webgriffe_sylius_pagolight_plugin.payment.status',
                ['paymentId' => $payment->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );

            throw new HttpResponse($this->twig->render(
                '@WebgriffeSyliusPagolightPlugin/after_pay.html.twig',
                [
                    'afterUrl' => $captureToken->getAfterUrl(),
                    'paymentStatusUrl' => $paymentStatusUrl,
                ],
            ));
        }

        $captureUrl = $captureToken->getTargetUrl();

        $cancelToken = $this->tokenFactory->createToken($captureToken->getGatewayName(), $captureToken->getDetails(), 'payum_cancel_do', [], $captureToken->getAfterUrl());
        $cancelUrl = $cancelToken->getTargetUrl();

        $notifyToken = $this->tokenFactory->createNotifyToken($captureToken->getGatewayName(), $captureToken->getDetails());
        $notifyUrl = $notifyToken->getTargetUrl();

        $convertPaymentToContract = new ConvertPaymentToContract(
            $payment,
            $captureUrl,
            $cancelUrl,
            $cancelUrl,
            $notifyUrl,
            'pagolight',
            [3, 6, 12, 24],
            [],
        );
        $this->gateway->execute($convertPaymentToContract);
        $contract = $convertPaymentToContract->getContract();
        Assert::isInstanceOf($contract, Contract::class);

        $createContract = new CreateContract($contract);
        $this->gateway->execute($createContract);
        $contractCreateResult = $createContract->getResult();
        Assert::isInstanceOf($contractCreateResult, ContractCreateResult::class);

        $payment->setDetails(
            PaymentDetailsHelper::createFromContractCreateResult($contractCreateResult),
        );

        throw new HttpRedirect($contractCreateResult->getRedirectUrl());
    }

    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof SyliusPaymentInterface
        ;
    }
}
