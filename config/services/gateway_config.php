<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder;
use Webgriffe\SyliusPagolightPlugin\Payum\PagolightGatewayFactory;
use Webgriffe\SyliusPagolightPlugin\Payum\PagolightProGatewayFactory;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_pagolight.gateway_factory_builder', GatewayFactoryBuilder::class)
        ->args([
            PagolightGatewayFactory::class,
            PagolightProGatewayFactory::class,
        ])
        ->tag('payum.gateway_factory_builder', ['factory' => 'pagolight'])
        ->tag('payum.gateway_factory_builder', ['factory' => 'pagolight_pro'])
    ;

};
