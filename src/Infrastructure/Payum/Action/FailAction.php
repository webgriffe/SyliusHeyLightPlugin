<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Request\Fail;

final class FailAction implements ActionInterface
{
    /**
     * @param Fail $request
     */
    public function execute($request): void
    {
        /** @var PaymentInterface $payment */
        $payment = $request->getModel();

        $payment->setDetails([
            'esito' => 'annullato',
        ]);
    }

    public function supports($request): bool
    {
        return
            $request instanceof Fail &&
            $request->getModel() instanceof PaymentInterface
        ;
    }
}
