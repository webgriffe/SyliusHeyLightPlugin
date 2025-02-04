<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Payum\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Webgriffe\SyliusHeylightPlugin\Client\ClientInterface;
use Webgriffe\SyliusHeylightPlugin\Payum\HeylightApi;
use Webgriffe\SyliusHeylightPlugin\Payum\Request\Api\Auth;
use Webmozart\Assert\Assert;

/**
 * This action is responsible for authenticating the client against the Heylight API.
 * It will use a layer of cache to speed up the authentication process.
 */
final class AuthAction implements ActionInterface
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * @param Auth|mixed $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        Assert::isInstanceOf($request, Auth::class);

        $heylightApi = $request->getHeylightApi();
        $client = $this->client;

        $bearerToken = $this->cache->get($this->getCacheKey($heylightApi), function (ItemInterface $item) use ($heylightApi, $client) {
            $item->expiresAfter(82_800); // 23 hours (the token expires after 24 hours)

            $client->setSandbox($heylightApi->isSandBox());

            return $this->client->auth($heylightApi->getMerchantKey());
        });
        Assert::stringNotEmpty($bearerToken);

        $request->setBearerToken($bearerToken);
    }

    public function supports($request): bool
    {
        return $request instanceof Auth;
    }

    private function getCacheKey(HeylightApi $heylightApi): string
    {
        return sprintf(
            'webgriffe_heylight_bearer_token_mer_%s_sand_%s',
            hash('md5', $heylightApi->getMerchantKey()),
            (string) $heylightApi->isSandBox(),
        );
    }
}
