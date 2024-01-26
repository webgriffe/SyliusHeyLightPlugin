<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Webgriffe\SyliusPagolightPlugin\Domain\Converter\ContractConverterInterface;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Request\ConvertPaymentToContract;
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
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        Assert::isInstanceOf($request, ConvertPaymentToContract::class);

        $contract = $this->contractConverter->convertFromPayment(
            $request->getPayment(),
            $request->getSuccessUrl(),
            $request->getFailureUrl(),
            $request->getCancelUrl(),
        );

        $request->setContract($contract);
    }

    public function supports($request): bool
    {
        return $request instanceof ConvertPaymentToContract;
    }
}
