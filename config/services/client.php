<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Http\Discovery\Psr18Client;
use Webgriffe\SyliusPagolightPlugin\Client\Client;
use Webgriffe\SyliusPagolightPlugin\Client\ClientInterface;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_pagolight.http_client', Psr18Client::class);

    $services->set('webgriffe_sylius_pagolight.client', Client::class)
        ->args([
            service('webgriffe_sylius_pagolight.http_client'),
            service('webgriffe_sylius_pagolight.http_client'),
        ])
    ;

    $services->alias(ClientInterface::class, 'webgriffe_sylius_pagolight.client');
};
