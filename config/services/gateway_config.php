<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder;
use Webgriffe\SyliusHeylightPlugin\Payum\HeylightApi;
use Webgriffe\SyliusHeylightPlugin\Payum\HeylightBnplGatewayFactory;
use Webgriffe\SyliusHeylightPlugin\Payum\HeylightFinancingGatewayFactory;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_heylight.gateway_factory_builder', GatewayFactoryBuilder::class)
        ->args([
            HeylightBnplGatewayFactory::class,
            HeylightFinancingGatewayFactory::class,
        ])
        ->tag('payum.gateway_factory_builder', ['factory' => HeylightApi::HEYLIGHT_BNPL_GATEWAY_CODE])
        ->tag('payum.gateway_factory_builder', ['factory' => HeylightApi::HEYLIGHT_FINANCING_GATEWAY_CODE])
    ;

};
