<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Request\Cancel;
use Sylius\Component\Core\Model\PaymentInterface;

final class CancelAction implements ActionInterface
{
    /**
     * @param Cancel $request
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
            $request instanceof Cancel &&
            $request->getModel() instanceof PaymentInterface
        ;
    }
}
