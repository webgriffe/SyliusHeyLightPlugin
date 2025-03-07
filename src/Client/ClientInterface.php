<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Client;

use Webgriffe\SyliusHeylightPlugin\Client\Exception\ApplicationStatusFailedException;
use Webgriffe\SyliusHeylightPlugin\Client\Exception\AuthFailedException;
use Webgriffe\SyliusHeylightPlugin\Client\Exception\ClientException;
use Webgriffe\SyliusHeylightPlugin\Client\Exception\ContractCreateFailedException;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\Contract;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\Response\ApplicationStatusResult;
use Webgriffe\SyliusHeylightPlugin\Client\ValueObject\Response\ContractCreateResult;

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
     *
     * @throws ClientException
     * @throws ApplicationStatusFailedException
     */
    public function applicationStatus(array $contractsUuid, string $bearerToken): ApplicationStatusResult;
}
