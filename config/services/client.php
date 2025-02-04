<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use GuzzleHttp\Client as GuzzleHttpClient;
use Webgriffe\SyliusHeylightPlugin\Client\Client;
use Webgriffe\SyliusHeylightPlugin\Client\ClientInterface;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_heylight.http_client', GuzzleHttpClient::class);

    $services->set('webgriffe_sylius_heylight.client', Client::class)
        ->args([
            service('webgriffe_sylius_heylight.http_client'),
            service('webgriffe_sylius_heylight.logger'),
        ])
    ;

    $services->alias(ClientInterface::class, 'webgriffe_sylius_heylight.client');
};
