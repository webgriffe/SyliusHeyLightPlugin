<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Webgriffe\SyliusHeylightPlugin\Converter\ContractConverterInterface;
use Webgriffe\SyliusHeylightPlugin\Payum\Request\ConvertPaymentToContract;
use Webmozart\Assert\Assert;

final class ConvertPaymentToContractAction implements ActionInterface
{
    public function __construct(
        private readonly ContractConverterInterface $contractConverter,
    ) {
    }

    /**
     * @param ConvertPaymentToContract|mixed $request
     */
    #[\Override]
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        Assert::isInstanceOf($request, ConvertPaymentToContract::class);

        $contract = $this->contractConverter->convertFromPayment(
            $request->getPayment(),
            $request->getSuccessUrl(),
            $request->getFailureUrl(),
            $request->getCancelUrl(),
            $request->getWebhookUrl(),
            $request->getWebhookToken(),
            $request->getAllowedTerms(),
            $request->getAdditionalData(),
        );

        $request->setContract($contract);
    }

    #[\Override]
    public function supports($request): bool
    {
        return $request instanceof ConvertPaymentToContract;
    }
}
