<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Client;

use Webgriffe\SyliusPagolightPlugin\Client\Exception\AuthFailedException;
use Webgriffe\SyliusPagolightPlugin\Client\Exception\ClientException;

interface ClientInterface
{
    /**
     * @return string The bearer auth token needed for all the other requests
     *
     * @throws ClientException
     * @throws AuthFailedException
     */
    public function auth(string $merchantKey): string;
}
