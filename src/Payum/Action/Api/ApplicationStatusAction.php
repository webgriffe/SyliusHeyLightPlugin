<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Webgriffe\SyliusPagolightPlugin\Client\ClientInterface;
use Webgriffe\SyliusPagolightPlugin\Payum\PagolightApi;
use Webgriffe\SyliusPagolightPlugin\Payum\Request\Api\ApplicationStatus;
use Webgriffe\SyliusPagolightPlugin\Payum\Request\Api\Auth;
use Webmozart\Assert\Assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor Api and gateway are injected via container configuration
 */
final class ApplicationStatusAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use GatewayAwareTrait, ApiAwareTrait;

    public function __construct(
        private readonly ClientInterface $client,
    ) {
        $this->apiClass = PagolightApi::class;
    }

    /**
     * @param ApplicationStatus|mixed $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        Assert::isInstanceOf($request, ApplicationStatus::class);

        $pagolightApi = $this->api;
        Assert::isInstanceOf($pagolightApi, PagolightApi::class);

        $this->gateway->execute($auth = new Auth($pagolightApi));
        $bearerToken = $auth->getBearerToken();
        Assert::stringNotEmpty($bearerToken);

        $this->client->setSandbox($pagolightApi->isSandBox());
        $applicationStatus = $this->client->applicationStatus($request->getContractsUuid(), $bearerToken);

        $request->setResult($applicationStatus);
    }

    public function supports($request): bool
    {
        return $request instanceof ApplicationStatus;
    }
}
