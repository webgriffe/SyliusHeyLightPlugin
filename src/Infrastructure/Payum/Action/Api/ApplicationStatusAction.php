<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ClientInterface;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\PagolightApi;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Request\Api\ApplicationStatus;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Request\Api\Auth;
use Webmozart\Assert\Assert;

final class ApplicationStatusAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use GatewayAwareTrait, ApiAwareTrait;

    public function __construct(
        private readonly ClientInterface $client,
    ) {
        $this->apiClass = PagolightApi::class;
    }

    /**
     * @param ApplicationStatus $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $pagolightApi = $this->api;
        Assert::isInstanceOf($pagolightApi, PagolightApi::class);

        $this->gateway->execute($auth = new Auth($pagolightApi));
        $bearerToken = $auth->getBearerToken();

        $this->client->setSandbox($pagolightApi->isSandBox());
        $applicationStatus = $this->client->applicationStatus($request->getContractsUuid(), $bearerToken);

        $request->setResult($applicationStatus);
    }

    public function supports($request): bool
    {
        return $request instanceof ApplicationStatus;
    }
}
