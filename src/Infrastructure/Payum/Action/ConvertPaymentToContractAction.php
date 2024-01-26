<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Webgriffe\SyliusPagolightPlugin\Domain\Converter\ContractConverterInterface;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Request\ConvertPaymentToContract;

final class ConvertPaymentToContractAction implements ActionInterface
{
    public function __construct(
        private readonly ContractConverterInterface $contractConverter,
    ) {
    }

    /**
     * @param ConvertPaymentToContract $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

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
