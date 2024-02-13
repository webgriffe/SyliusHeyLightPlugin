<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Webgriffe\SyliusPagolightPlugin\Payum\Request\RemovePaymentWebhookToken;
use Webgriffe\SyliusPagolightPlugin\Repository\WebhookTokenRepositoryInterface;
use Webmozart\Assert\Assert;

final class RemovePaymentWebhookTokenAction implements ActionInterface
{
    public function __construct(
        private readonly WebhookTokenRepositoryInterface $webhookTokenRepository,
    ) {
    }

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        Assert::isInstanceOf($request, RemovePaymentWebhookToken::class);

        $this->webhookTokenRepository->remove($request->getWebhookToken());
    }

    public function supports($request): bool
    {
        return $request instanceof RemovePaymentWebhookToken;
    }
}
