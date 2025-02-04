<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusHeylightPlugin\Service;

use DateTimeImmutable;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webgriffe\SyliusHeylightPlugin\Client\ClientInterface;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\Contract;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\Response\ApplicationStatusResult;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\Response\ContractCreateResult;

final class DummyClient implements ClientInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    private bool $isSandBox = false;

    public function setSandbox(bool $isSandBox): void
    {
        $this->isSandBox = $isSandBox;
    }

    public function auth(string $merchantKey): string
    {
        return 'TOKEN';
    }

    public function contractCreate(Contract $contract, string $bearerToken): ContractCreateResult
    {
        // Redirect to any other page. This is just a dummy implementation.
        $homepageUrl = $this->urlGenerator->generate('sylius_shop_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return new ContractCreateResult(
            $homepageUrl,
            '123456',
            new DateTimeImmutable(),
        );
    }

    public function applicationStatus(array $contractsUuid, string $bearerToken): ApplicationStatusResult
    {
        return new ApplicationStatusResult([]);
    }
}
