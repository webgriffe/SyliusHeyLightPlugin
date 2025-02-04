<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Payum\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Webgriffe\SyliusHeylightPlugin\Client\ClientInterface;
use Webgriffe\SyliusHeylightPlugin\Payum\HeylightApi;
use Webgriffe\SyliusHeylightPlugin\Payum\Request\Api\Auth;
use Webgriffe\SyliusHeylightPlugin\Payum\Request\Api\CreateContract;
use Webmozart\Assert\Assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor Api and gateway are injected via container configuration
 */
final class CreateContractAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use GatewayAwareTrait, ApiAwareTrait;

    public function __construct(
        private readonly ClientInterface $client,
    ) {
        $this->apiClass = HeylightApi::class;
    }

    /**
     * @param CreateContract|mixed $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        Assert::isInstanceOf($request, CreateContract::class);

        $heylightApi = $this->api;
        Assert::isInstanceOf($heylightApi, HeylightApi::class);

        $this->gateway->execute($auth = new Auth($heylightApi));
        $bearerToken = $auth->getBearerToken();
        Assert::stringNotEmpty($bearerToken);

        $this->client->setSandbox($heylightApi->isSandBox());
        $contractCreateResult = $this->client->contractCreate($request->getContract(), $bearerToken);

        $request->setResult($contractCreateResult);
    }

    public function supports($request): bool
    {
        return $request instanceof CreateContract;
    }
}
