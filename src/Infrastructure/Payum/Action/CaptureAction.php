<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\Exception\ClientException;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\ApplicationStatusResult;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\Contract;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\ContractCreateResult;
use Webgriffe\SyliusPagolightPlugin\Domain\PaymentDetailsHelper;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Request\Api\ApplicationStatus;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Request\Api\CreateContract;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Request\ConvertPaymentToContract;
use Webmozart\Assert\Assert;

/**
 * @psalm-type PaymentDetails array{contract_uuid: string, redirect_url: string, created_at: string, expire_at: string, status?: string}
 */
final class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

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
            $contractUuid = PaymentDetailsHelper::getContractUuid($paymentDetails);

            $applicationStatus = new ApplicationStatus([$contractUuid]);
            $this->gateway->execute($applicationStatus);
            $applicationStatusResult = $applicationStatus->getResult();
            Assert::isInstanceOf($applicationStatusResult, ApplicationStatusResult::class);

            $paymentDetails = PaymentDetailsHelper::addPaymentStatus(
                $paymentDetails,
                $applicationStatusResult->getStatusByContractUuid($contractUuid),
            );
            $payment->setDetails($paymentDetails);

            return;
        }

        $captureUrl = $token->getTargetUrl();
        $convertPaymentToContract = new ConvertPaymentToContract($payment, $captureUrl, $captureUrl, $captureUrl);
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
