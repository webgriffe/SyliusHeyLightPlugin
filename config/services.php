<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder;
use Webgriffe\SyliusPagolightPlugin\Form\Type\SyliusPagolightGatewayConfigurationType;
use Webgriffe\SyliusPagolightPlugin\Payum\PagolightGatewayFactory;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $containerConfigurator->import('services/*.php');

    $services->set('webgriffe_sylius_pagolight.gateway_factory_builder', GatewayFactoryBuilder::class)
        ->args([
            PagolightGatewayFactory::class,
        ])
        ->tag('payum.gateway_factory_builder', ['factory' => 'pagolight'])
    ;

    $services->set('webgriffe_sylius_pagolight.form.type.gateway_configuration', SyliusPagolightGatewayConfigurationType::class)
        ->tag('sylius.gateway_configuration_type', ['type' => 'pagolight', 'label' => 'Pagolight'])
        ->tag('form.type')
    ;
};
