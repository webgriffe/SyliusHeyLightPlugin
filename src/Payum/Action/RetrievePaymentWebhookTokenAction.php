<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Webgriffe\SyliusHeylightPlugin\Payum\Request\RetrievePaymentWebhookToken;
use Webgriffe\SyliusHeylightPlugin\Repository\WebhookTokenRepositoryInterface;
use Webmozart\Assert\Assert;

final class RetrievePaymentWebhookTokenAction implements ActionInterface
{
    public function __construct(
        private readonly WebhookTokenRepositoryInterface $webhookTokenRepository,
    ) {
    }

    /**
     * @param RetrievePaymentWebhookToken|mixed $request
     */
    #[\Override]
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        Assert::isInstanceOf($request, RetrievePaymentWebhookToken::class);

        $webhookToken = $this->webhookTokenRepository->findOneByPayment($request->getPayment());

        $request->setWebhookToken($webhookToken);
    }

    #[\Override]
    public function supports($request): bool
    {
        return $request instanceof RetrievePaymentWebhookToken;
    }
}
