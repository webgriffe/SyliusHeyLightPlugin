<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Capture;
use Payum\Core\Request\Convert;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\Contract;
use Webgriffe\SyliusPagolightPlugin\Domain\Converter\ContractConverterInterface;
use Webmozart\Assert\Assert;

final class ConvertPaymentAction implements ActionInterface
{
    public function __construct(
        private readonly ContractConverterInterface $contractConverter,
    ) {
    }

    /**
     * @param Convert $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var Capture $capture */
        $capture = $request->getSource();

        $payment = $capture->getModel();
        Assert::isInstanceOf($payment, PaymentInterface::class);

        $token = $capture->getToken();
        Assert::isInstanceOf($token, TokenInterface::class);

        $contract = $this->contractConverter->convertFromPayment(
            $payment,
            $token->getTargetUrl(),
            $token->getTargetUrl(),
            $token->getTargetUrl(),
        );

        $request->setResult($contract);
    }

    public function supports($request): bool
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof Capture &&
            $request->getTo() === Contract::class
        ;
    }
}
