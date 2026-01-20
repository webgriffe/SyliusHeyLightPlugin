<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Webgriffe\SyliusHeylightPlugin\Payum\Request\RemovePaymentWebhookToken;
use Webgriffe\SyliusHeylightPlugin\Repository\WebhookTokenRepositoryInterface;
use Webmozart\Assert\Assert;

final class RemovePaymentWebhookTokenAction implements ActionInterface
{
    public function __construct(
        private readonly WebhookTokenRepositoryInterface $webhookTokenRepository,
    ) {
    }

    #[\Override]
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        Assert::isInstanceOf($request, RemovePaymentWebhookToken::class);

        $this->webhookTokenRepository->remove($request->getWebhookToken());
    }

    #[\Override]
    public function supports($request): bool
    {
        return $request instanceof RemovePaymentWebhookToken;
    }
}
