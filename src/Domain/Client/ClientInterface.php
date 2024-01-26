<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Domain\Client;

use Webgriffe\SyliusPagolightPlugin\Domain\Client\Exception\AuthFailedException;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\Exception\ClientException;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\Exception\ContractCreateFailedException;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\ApplicationStatusResult;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\Contract;
use Webgriffe\SyliusPagolightPlugin\Domain\Client\ValueObject\ContractCreateResult;

interface ClientInterface
{
    public function setSandbox(bool $isSandBox): void;

    /**
     * @return string The bearer auth token needed for all the other requests
     *
     * @throws ClientException
     * @throws AuthFailedException
     */
    public function auth(string $merchantKey): string;

    /**
     * @throws ClientException
     * @throws ContractCreateFailedException
     */
    public function contractCreate(Contract $contract, string $bearerToken): ContractCreateResult;

    /**
     * @param string[] $contractsUuid
     */
    public function applicationStatus(array $contractsUuid, string $bearerToken): ApplicationStatusResult;
}
