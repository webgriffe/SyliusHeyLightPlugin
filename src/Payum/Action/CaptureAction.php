<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Client\Exception\ClientException;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\ApplicationStatusResult;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\Contract;
use Webgriffe\SyliusPagolightPlugin\Client\ValueObject\ContractCreateResult;
use Webgriffe\SyliusPagolightPlugin\PaymentDetailsHelper;
use Webgriffe\SyliusPagolightPlugin\Payum\Request\Api\ApplicationStatus;
use Webgriffe\SyliusPagolightPlugin\Payum\Request\Api\CreateContract;
use Webgriffe\SyliusPagolightPlugin\Payum\Request\ConvertPaymentToContract;
use Webmozart\Assert\Assert;

/**
 * @psalm-type PaymentDetails array{contract_uuid: string, redirect_url: string, created_at: string, expire_at: string, status?: string}
 *
 * @psalm-suppress PropertyNotSetInConstructor Api and gateway are injected via container configuration
 */
final class CaptureAction implements ActionInterface, GatewayAwareInterface, GenericTokenFactoryAwareInterface
{
    use GatewayAwareTrait, GenericTokenFactoryAwareTrait;

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

        $token = $request->getToken();
        Assert::isInstanceOf($token, TokenInterface::class);

        /** @var PaymentDetails|array{} $paymentDetails */
        $paymentDetails = $payment->getDetails();

        if ($paymentDetails !== []) {
            PaymentDetailsHelper::assertPaymentDetailsAreValid($paymentDetails);
            /** @psalm-suppress InvalidArgument */
            $contractUuid = PaymentDetailsHelper::getContractUuid($paymentDetails);

            $applicationStatus = new ApplicationStatus([$contractUuid]);
            $this->gateway->execute($applicationStatus);
            $applicationStatusResult = $applicationStatus->getResult();
            Assert::isInstanceOf($applicationStatusResult, ApplicationStatusResult::class);

            /** @psalm-suppress InvalidArgument */
            $paymentDetails = PaymentDetailsHelper::addPaymentStatus(
                $paymentDetails,
                $applicationStatusResult->getStatusByContractUuid($contractUuid),
            );
            $payment->setDetails($paymentDetails);

            return;
        }

        $captureUrl = $token->getTargetUrl();

        $cancelToken = $this->tokenFactory->createToken($token->getGatewayName(), $token->getDetails(), 'payum_cancel_do', [], $token->getAfterUrl());
        $cancelUrl = $cancelToken->getTargetUrl();

        $convertPaymentToContract = new ConvertPaymentToContract($payment, $captureUrl, $cancelUrl, $cancelUrl);
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
