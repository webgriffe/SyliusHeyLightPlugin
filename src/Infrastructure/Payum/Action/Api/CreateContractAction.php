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
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Request\Api\Auth;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Request\Api\CreateContract;
use Webmozart\Assert\Assert;

final class CreateContractAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use GatewayAwareTrait, ApiAwareTrait;

    public function __construct(
        private readonly ClientInterface $client,
    ) {
        $this->apiClass = PagolightApi::class;
    }

    /**
     * @param CreateContract|mixed $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        Assert::isInstanceOf($request, CreateContract::class);

        $pagolightApi = $this->api;
        Assert::isInstanceOf($pagolightApi, PagolightApi::class);

        $this->gateway->execute($auth = new Auth($pagolightApi));
        $bearerToken = $auth->getBearerToken();
        Assert::stringNotEmpty($bearerToken);

        $this->client->setSandbox($pagolightApi->isSandBox());
        $contractCreateResult = $this->client->contractCreate($request->getContract(), $bearerToken);

        $request->setResult($contractCreateResult);
    }

    public function supports($request): bool
    {
        return $request instanceof CreateContract;
    }
}
